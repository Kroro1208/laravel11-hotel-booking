<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Reservation;
use App\Models\ReservationSlot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class ReservationController extends Controller
{
    public function store(Request $request)
    {
        // todo FormRequestにかく
        $validatedData = $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'reservation_slot_id' => 'required|exists:reservation_slots,id',
            'guest_name' => 'required|string|max:255',
            'guest_email' => 'required|email',
            'number_of_guests' => 'required|integer|min:1',
            // その他の必要なバリデーションルール
        ]);

        DB::beginTransaction();

        try {
            $slot = ReservationSlot::findOrFail($validatedData['reservation_slot_id']);

            if ($slot->getAvailableRooms() < 1) {
                throw new \Exception('選択された日付の部屋は既に満室です。');
            }

            $reservation = Reservation::create([
                'user_id' => auth()->id(),
                'plan_id' => $validatedData['plan_id'],
                'reservation_slot_id' => $validatedData['reservation_slot_id'],
                'guest_name' => $validatedData['guest_name'],
                'guest_email' => $validatedData['guest_email'],
                'number_of_guests' => $validatedData['number_of_guests'],
                'status' => 'confirmed',
                // その他の必要なフィールド
            ]);

            $slot->book();  // ReservationSlotモデルのbookメソッドを呼び出し

            DB::commit();

            return redirect()->route('user.reservations.show', $reservation)
                ->with('success', '予約が完了しました。');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

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
