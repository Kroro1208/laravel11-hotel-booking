<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\ReservationSlot;
use App\Models\RoomType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReservationSlotController extends Controller
{
    public function create()
    {
        $roomTypes = RoomType::all();
        return view('admin.reservation_slots.create', compact('roomTypes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'room_types' => 'required|array',
            'room_counts' => 'required|array',
        ]);

        $startDate = new \DateTime($request->start_date);
        $endDate = new \DateTime($request->end_date);

        while ($startDate <= $endDate) {
            foreach ($request->room_types as $index => $roomTypeId) {
                ReservationSlot::create([
                    'date' => $startDate->format('Y-m-d'),
                    'room_type_id' => $roomTypeId,
                    'total_rooms' => $request->room_counts[$index],
                    'booked_rooms' => 0,
                    'status' => 'available',
                ]);
            }
            $startDate->modify('+1 day');
        }

        return to_route('admin.reservation_slots.index')->with('success', '予約枠を作成しました。');
    }

    public function edit(Plan $plan)
    {
        $reservationSlots = $plan->reservationSlots()->orderBy('date')->get();
        return view('admin.reservation_slots.edit', compact('plan', 'reservationSlots'));
    }

    public function update(Request $request, Plan $plan)
    {
        $request->validate([
            'slots' => 'required|array',
            'slots.*.date' => 'required|date',
            'slots.*.room_type_id' => 'required|exists:room_types,id',
            'slots.*.total_rooms' => 'required|integer|min:0',
        ]);

        foreach ($request->slots as $slotData) {
            ReservationSlot::updateOrCreate(
                [
                    'plan_id' => $plan->id,
                    'date' => $slotData['date'],
                    'room_type_id' => $slotData['room_type_id']
                ],
                ['total_rooms' => $slotData['total_rooms']]
            );
        }

        return redirect()->route('plans.show', $plan)->with('success', '予約枠が更新されました。');
    }
}
