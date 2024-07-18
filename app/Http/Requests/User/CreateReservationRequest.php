<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\ReservationSlot;
use Carbon\Carbon;

class CreateReservationRequest extends FormRequest
{
    public function rules()
    {
        return [
            'plan_id' => 'required|exists:plans,id',
            'room_type_id' => 'required|exists:room_types,id',
            'check_in_date' => 'required|date|after_or_equal:today',
            'check_out_date' => 'required|date|after:check_in_date',
            'room_count' => 'required|integer|min:1',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $planId = $this->input('plan_id');
            $roomTypeId = $this->input('room_type_id');
            $checkInDate = Carbon::parse($this->input('check_in_date'));
            $checkOutDate = Carbon::parse($this->input('check_out_date'));
            $roomCount = $this->input('room_count');

            $unavailableDates = ReservationSlot::where('plan_id', $planId)
                ->where('room_type_id', $roomTypeId)
                ->whereBetween('date', [$checkInDate, $checkOutDate->subDay()])
                ->where(function ($query) use ($roomCount) {
                    $query->where('status', ReservationSlot::STATUS_UNAVAILABLE)
                        ->orWhereRaw('total_rooms - booked_rooms < ?', [$roomCount]);
                })
                ->pluck('date')
                ->map(function ($date) {
                    return Carbon::parse($date)->format('Y-m-d');
                })
                ->toArray();

            if (!empty($unavailableDates)) {
                $validator->errors()->add('reservation', '選択された日付範囲内で利用できない日があります: ' . implode(', ', $unavailableDates));
            }
        });
    }
}
