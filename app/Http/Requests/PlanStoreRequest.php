<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PlanStoreRequest extends FormRequest
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
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240', // 10MBに制限
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'room_type' => [
                'required',
                Rule::exists('room_types', 'id') // room_typesテーブルのidカラムに存在することを確認
            ],
            'room_count' => 'required|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'タイトルは必須です。',
            'title.max' => 'タイトルは255文字以内で入力してください。',
            'description.required' => '説明は必須です。',
            'image.required' => '画像は必須です。',
            'image.image' => '有効な画像ファイルを選択してください。',
            'image.max' => '画像サイズは10MB以内にしてください。',
            'start_date.required' => '開始日は必須です。',
            'start_date.date' => '有効な日付を入力してください。',
            'end_date.required' => '終了日は必須です。',
            'end_date.date' => '有効な日付を入力してください。',
            'end_date.after_or_equal' => '終了日は開始日以降の日付を選択してください。',
            'room_type.required' => '部屋タイプを選択してください。',
            'room_type.exists' => '選択された部屋タイプは無効です。',
            'room_count.required' => '予約部屋数を入力してください。',
            'room_count.integer' => '予約部屋数は整数で入力してください。',
            'room_count.min' => '予約部屋数は1以上である必要があります。',
        ];
    }
}
