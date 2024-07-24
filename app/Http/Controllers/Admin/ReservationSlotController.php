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
use Illuminate\Validation\ValidationException;
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
        Log::info('予約枠作成開始', ['request' => $request->all()]);

        try {
            $roomType = RoomType::findOrFail($request->input('room_type_id'));

            $validatedData = $request->validated();

            Log::info('バリデーション成功', ['validatedData' => $validatedData]);

            $startDate = Carbon::parse($validatedData['start_date']);
            $endDate = Carbon::parse($validatedData['end_date']);
            Log::info('日付パース成功', ['startDate' => $startDate, 'endDate' => $endDate]);

            DB::beginTransaction();

            try {
                $createdOrUpdatedSlots = 0;
                for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
                    $slot = ReservationSlot::updateOrCreate(
                        [
                            'room_type_id' => $roomType->id,
                            'date' => $date->format('Y-m-d'),
                        ],
                        [
                            'available_rooms' => $validatedData['available_rooms'],
                            'price' => $validatedData['price'],
                        ]
                    );

                    if ($slot->wasRecentlyCreated) {
                        $slot->booked_rooms = 0;
                        $slot->save();
                    }

                    $createdOrUpdatedSlots++;
                    Log::info('予約枠作成/更新', [
                        'date' => $date->format('Y-m-d'),
                        'slotId' => $slot->id,
                        'isNewlyCreated' => $slot->wasRecentlyCreated,
                    ]);
                }

                DB::commit();
                Log::info('予約枠作成/更新完了', ['createdOrUpdatedSlots' => $createdOrUpdatedSlots]);

                return to_route('reservationSlot.index')
                    ->with('success', "{$createdOrUpdatedSlots}件の予約枠が正常に作成または更新されました。");
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('データベース操作エラー', [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }
        } catch (ValidationException $e) {
            Log::error('バリデーションエラー', ['errors' => $e->errors()]);
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('予約枠作成エラー', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withInput()
                ->with('error', '予約枠の作成中にエラーが発生しました: ' . $e->getMessage());
        }
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
