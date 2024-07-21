<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\ReservationSlot;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReservationSlotController extends Controller
{


    public function update(Request $request, Plan $plan): RedirectResponse
    {
        $data = $request->validate([
            'slots' => 'required|array',
            'update_slot' => 'required|integer|exists:reservation_slots,id'
        ]);

        $slotId = $data['update_slot'];
        $slotData = $data['slots'][$slotId];

        $slot = ReservationSlot::findOrFail($slotId);

        if ($slotData['total_rooms'] < $slot->booked_rooms) {
            return redirect()->back()->with('error', '部屋数が足りません。');
        }

        $slot->update([
            'total_rooms' => $slotData['total_rooms']
        ]);

        return redirect()->route('plan.show', $plan)->with('success', '予約枠が更新されました');
    }
}
