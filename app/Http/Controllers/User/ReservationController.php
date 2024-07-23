<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Reservation;
use App\Models\ReservationSlot;
use App\Models\RoomType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReservationController extends Controller
{
    public function store(Request $request)
    {
        Log::info('Reservation store method called', $request->all());

        $validatedData = $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'date' => 'required|date',
            'room_type_id' => 'required|exists:room_types,id',
            'room_count' => 'required|integer|min:1',
            'guest_name' => 'required|string|max:255',
            'guest_email' => 'required|email',
            'number_of_guests' => 'required|integer|min:1',
        ]);

        Log::info('Validation passed', $validatedData);

        DB::beginTransaction();

        try {
            $plan = Plan::findOrFail($validatedData['plan_id']);
            $date = Carbon::parse($validatedData['date']);

            Log::info("Fetching reservation slot for date: {$date}");

            $slot = ReservationSlot::where('plan_id', $plan->id)
                ->where('room_type_id', $validatedData['room_type_id'])
                ->where('date', $date)
                ->firstOrFail();

            Log::info("Reservation slot found", ['slot_id' => $slot->id, 'available_rooms' => $slot->getAvailableRooms()]);

            if ($slot->getAvailableRooms() < $validatedData['room_count']) {
                throw new \Exception('選択された日付の部屋数が不足しています。');
            }

            $reservation = Reservation::create([
                'user_id' => auth()->id(),
                'plan_id' => $validatedData['plan_id'],
                'booking_number' => $this->generateBookingNumber(),
                'checkIn_date' => $date,
                'checkOut_date' => $date->copy()->addDay(),
                'total_price' => $plan->price * $validatedData['room_count'],
                'status' => 'confirmed',
                'message' => $request->input('message'),
            ]);

            Log::info("Reservation created", ['reservation_id' => $reservation->id, 'booking_number' => $reservation->booking_number]);

            $booked = $slot->book($validatedData['room_count']);

            if (!$booked) {
                throw new \Exception('予約処理中にエラーが発生しました。');
            }

            Log::info("Slot booked successfully");

            DB::commit();

            Log::info("Transaction committed successfully");

            return redirect()->route('user.reservations.show', $reservation)
                ->with('success', '予約が完了しました。');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error in reservation process", ['error' => $e->getMessage()]);
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    private function generateBookingNumber()
    {
        return 'BK' . strtoupper(uniqid());
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
