<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ReservationSlotController\StoreRequest;
use App\Models\ReservationSlot;
use App\Models\RoomType;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class ReservationSlotController extends Controller
{

    public function index(): View
    {
        $reservationSlots = ReservationSlot::with('roomType')
            ->orderBy('date')
            ->paginate(20);

        return view('backend.reservationSlots.index', [
            'reservationSlots' => $reservationSlots
        ]);
    }
    public function create(): View
    {
        // 全ての部屋タイプを取得
        $roomTypes = RoomType::where('is_active', true)->get();

        // 日付範囲の設定（例：現在から1年後まで）
        $startDate = now()->format('Y-m-d');
        $endDate = now()->addYear()->format('Y-m-d');

        return view('backend.reservationSlots.create', compact('roomTypes', 'startDate', 'endDate'));
    }

    public function store(StoreRequest $request): RedirectResponse
    {
        try {
            $createdOrUpdatedSlots = $this->createOrUpdateReservationSlots($request);
            return to_route('reservationSlot.index')
                ->with('success', "{$createdOrUpdatedSlots}件の予約枠が正常に作成または更新されました。");
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', '予約枠の作成中にエラーが発生しました: ' . $e->getMessage());
        }
    }

    private function createOrUpdateReservationSlots(StoreRequest $request): int
    // 指定された日付範囲内の各日に対して予約枠を作成または更新する詳細なロジック
    {
        $validatedData = $request->validated();
        $roomType = RoomType::findOrFail($validatedData['room_type_id']);
        $startDate = Carbon::parse($validatedData['start_date']);
        $endDate = Carbon::parse($validatedData['end_date']);

        return DB::transaction(function () use ($roomType, $validatedData, $startDate, $endDate) {
            $createdOrUpdatedSlots = 0;

            for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
                $this->updateOrCreateSlot($roomType, $date, $validatedData);
                $createdOrUpdatedSlots++;
            }
            return $createdOrUpdatedSlots; // 何日分の予約が作られたか（または更新されたか）」をカウント
        });
    }

    private function updateOrCreateSlot(RoomType $roomType, Carbon $date, array $validatedData): void
    {
        // 特定の日付と部屋タイプに対する個々の予約枠を更新または作成するロジック
        ReservationSlot::updateOrCreate(
            [
                'room_type_id' => $roomType->id,
                'date' => $date->format('Y-m-d'),
            ],
            [
                'available_rooms' => $validatedData['available_rooms'],
                'price' => $validatedData['price'],
            ]
        );
    }

    public function edit(ReservationSlot $reservationSlot)
    {
        $roomTypes = RoomType::all();
        return view('backend.reservationSlots.edit', compact('reservationSlot', 'roomTypes'));
    }

    public function update(Request $request, ReservationSlot $reservationSlot)
    {
        $validatedData = $request->validate([
            'room_type_id' => 'required|exists:room_types,id',
            'date' => 'required|date',
            'available_rooms' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
        ]);

        $reservationSlot->update($validatedData);

        return redirect()->route('reservationSlot.index')
            ->with('success', '予約枠が正常に更新されました。');
    }

    public function destroy(ReservationSlot $reservationSlot)
    {
        $reservationSlot->delete();

        return redirect()->route('reservationSlot.index')
            ->with('success', '予約枠が正常に削除されました。');
    }
}
