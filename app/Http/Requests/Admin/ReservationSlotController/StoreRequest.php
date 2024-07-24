<?php

namespace App\Http\Requests\Admin\ReservationSlotController;

use App\Models\RoomType;
use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $roomType = RoomType::findOrFail($this->input('room_type_id'));
        return [
            'room_type_id' => 'required|exists:room_types,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'available_rooms' => [
                'required',
                'integer',
                'min:1',
                "max:{$roomType->number_of_rooms}",
            ],
            'price' => 'required|numeric|min:0',
        ];
    }
}
