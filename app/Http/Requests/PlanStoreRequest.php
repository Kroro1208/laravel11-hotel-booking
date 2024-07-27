<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PlanStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'images' => 'required|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'room_types' => 'required|array',
            'room_types.*' => 'exists:room_types,id',
        ];
    }
}

class PlanUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'images' => 'sometimes|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'room_types' => 'required|array',
            'room_types.*' => 'exists:room_types,id',
        ];
    }
}
