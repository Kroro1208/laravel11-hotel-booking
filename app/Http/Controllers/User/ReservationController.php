<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\ReservationSlot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReservationController extends Controller
{
    // ... 他のメソッド ...

    public function cancel(Reservation $reservation)
    {
        return DB::transaction(function () use ($reservation) {
            $checkInDate = $reservation->checkIn_date;
            $checkOutDate = $reservation->checkOut_date;
            $roomCount = $reservation->room_count;

            $reservationSlots = ReservationSlot::where('plan_id', $reservation->plan_id)
                ->where('room_type_id', $reservation->room_type_id)
                ->whereBetween('date', [$checkInDate, $checkOutDate->subDay()])
                ->get();

            $allCancelled = true;
            foreach ($reservationSlots as $slot) {
                if (!$slot->cancelBooking($roomCount)) {
                    $allCancelled = false;
                    break;
                }
            }

            if ($allCancelled) {
                $reservation->status = 'cancelled';
                $reservation->save();
                return redirect()->back()->with('success', '予約がキャンセルされました。');
            } else {
                return redirect()->back()->with('error', '予約のキャンセルに失敗しました。');
            }
        });
    }
}
