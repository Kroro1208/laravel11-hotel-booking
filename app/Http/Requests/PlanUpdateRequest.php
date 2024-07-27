<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PlanUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

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
            'room_count' => 'required|array',
            'room_count.*' => 'required|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'プラン名は必須です。',
            'title.max' => 'プラン名は255文字以内で入力してください。',
            'description.required' => '詳細は必須です。',
            'start_date.required' => '開始日は必須です。',
            'end_date.required' => '終了日は必須です。',
            'end_date.after_or_equal' => '終了日は開始日以降の日付を選択してください。',
            'images.*.image' => 'アップロードされたファイルは画像でなければなりません。',
            'images.*.mimes' => '画像はjpeg, png, jpg, gif形式のみ許可されています。',
            'images.*.max' => '画像サイズは2MB以下でなければなりません。',
            'room_types.required' => '少なくとも1つの部屋タイプを選択してください。',
            'room_types.*.exists' => '選択された部屋タイプが無効です。',
            'room_count.required' => '部屋数を入力してください。',
            'room_count.*.required' => '各部屋タイプの部屋数を入力してください。',
            'room_count.*.integer' => '部屋数は整数で入力してください。',
            'room_count.*.min' => '部屋数は1以上で入力してください。',
        ];
    }
}
