<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PlanStoreRequest;
use App\Http\Requests\PlanUpdateRequest;
use App\Models\Plan;
use App\Models\RoomType;
use App\Models\PlanRoom;
use App\Models\ReservationSlot;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PlanController extends Controller
{
    public function index(): View
    {
        $plans = Plan::with(['planRooms.roomType', 'reservations'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('backend.plan.index', [
            'plans' => $plans
        ]);
    }

    public function create(): View
    {
        $roomTypes = RoomType::all();
        return view('backend.plan.create', compact('roomTypes'));
    }

    public function store(PlanStoreRequest $request)
    {
        DB::beginTransaction();
        try {
            $plan = Plan::create([
                'image' => $request->file('image')->store('plan_images', 'public'),
                'title' => $request->title,
                'description' => $request->description,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'price' => $request->price,
                'is_reserved' => false,
            ]);

            $startDate = Carbon::parse($request->start_date);
            $endDate = Carbon::parse($request->end_date);

            foreach ($request->room_types as $index => $roomTypeId) {
                $roomType = RoomType::findOrFail($roomTypeId);
                PlanRoom::create([
                    'plan_id' => $plan->id,
                    'room_type_id' => $roomType->id,
                    'room_count' => $request->room_counts[$index],
                ]);

                // プランの開始日から終了日まで、各日に対して予約枠を作成
                $currentDate = $startDate->copy();
                while ($currentDate <= $endDate) {
                    ReservationSlot::create([
                        'plan_id' => $plan->id,
                        'room_type_id' => $roomType->id,
                        'date' => $currentDate->format('Y-m-d'),
                        'total_rooms' => $request->room_counts[$index],
                        'booked_rooms' => 0,
                        'status' => 'available',
                    ]);
                    $currentDate->addDay();
                }
            }

            DB::commit();
            return to_route('plan.index')->with('success', 'プランが正常に作成されました。');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'プランの作成中にエラーが発生しました: ' . $e->getMessage());
        }
    }

    public function show(Plan $plan)
    {
        // プランに関連する予約枠情報を取得
        $reservationSlots = $plan->reservationSlots()
            ->with('roomType')
            ->orderBy('date')
            ->get()
            ->groupBy('room_type_id');

        return view('backend.plan.show', compact('plan', 'reservationSlots'));
    }
    public function updateReservationStatus($planId)
    {
        $plan = Plan::findOrFail($planId);
        $reservationSlots = ReservationSlot::where('plan_id', $planId)->get();

        $allUnavailable = true;

        foreach ($reservationSlots as $slot) {
            if ($slot->total_rooms == $slot->booked_rooms) {
                $slot->status = 'unavailable';
            } elseif ($slot->total_rooms - $slot->booked_rooms <= 3) {
                $slot->status = 'few';
                $allUnavailable = false;
            } else {
                $slot->status = 'available';
                $allUnavailable = false;
            }
            $slot->save();
        }

        if ($allUnavailable) {
            $plan->is_reserved = true;
            $plan->save();
        }

        return back()->with('success', '予約状況が更新されました。');
    }


    public function edit(Plan $plan): View
    {
        $roomTypes = RoomType::all();
        $plan->load('planRooms.roomType');
        return view('backend.plan.edit', [
            'plan' => $plan,
            'roomTypes' => $roomTypes
        ]);
    }

    public function update(PlanUpdateRequest $request, Plan $plan): RedirectResponse
    {
        try {
            DB::transaction(function () use ($request, $plan) {
                // プラン情報を更新
                $plan->fill($request->validated());

                // 画像を更新
                if ($request->hasFile('image')) {
                    // 古い画像を削除
                    if ($plan->image) {
                        Storage::disk('public')->delete($plan->image);
                    }
                    $imagePath = $request->file('image')->store('plans', 'public');
                    $plan->image = $imagePath;
                }

                $plan->save();

                // 既存のplanRoomsを削除
                $plan->planRooms()->delete();

                // 新しいplanRoomsを作成
                foreach ($request->input('room_types') as $index => $roomTypeId) {
                    $plan->planRooms()->create([
                        'room_type_id' => $roomTypeId,
                        'room_count' => $request->input('room_counts')[$index],
                    ]);
                }

                // ReservationSlotsの更新（必要に応じて）
                $this->updateReservationSlots($plan);
            });

            return to_route('plan.index')->with('success', 'プランの更新に成功しました');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'プランの更新中にエラーが発生しました: ' . $e->getMessage());
        }
    }

    private function updateReservationSlots(Plan $plan)
    {
        // 既存のReservationSlotsを削除
        ReservationSlot::where('plan_id', $plan->id)->delete();

        // 新しいReservationSlotsを作成
        $currentDate = new \DateTime($plan->start_date);
        $endDate = new \DateTime($plan->end_date);

        while ($currentDate <= $endDate) {
            foreach ($plan->planRooms as $planRoom) {
                ReservationSlot::create([
                    'plan_id' => $plan->id,
                    'room_type_id' => $planRoom->room_type_id,
                    'date' => $currentDate->format('Y-m-d'),
                    'total_rooms' => $planRoom->room_count,
                    'booked_rooms' => 0,
                    'status' => 'available',
                ]);
            }
            $currentDate->modify('+1 day');
        }
    }

    public function destroy(Plan $plan): RedirectResponse
    {
        $plan->delete();
        return to_route('plan.index')->with('success', 'プランの削除に成功しました');
    }

    public function getAvailability($planId)
    {
        $plan = Plan::findOrFail($planId);
        $availability = $plan->getAvailabilityForDateRange($plan->start_date, $plan->end_date);

        $formattedAvailability = [];
        foreach ($availability as $date => $dayAvailability) {
            $formattedAvailability[$date] = [
                'available' => array_sum($dayAvailability) > 0,
                'roomTypes' => $dayAvailability
            ];
        }

        return response()->json($formattedAvailability);
    }
}
