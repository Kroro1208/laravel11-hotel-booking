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
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'price' => 'required|numeric|min:0',
            'image' => 'required|image',
            'room_types' => 'required|array',
            'room_types.*' => 'required|exists:room_types,id',
            'room_counts' => 'required|array',
            'room_counts.*' => 'required|integer|min:1',
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
            'price.required' => '価格は必須です。',
            'price.numeric' => '価格は数値で入力してください。',
            'price.min' => '価格は0以上である必要があります。',
            'room_types.required' => '少なくとも1つの部屋タイプを選択してください。',
            'room_types.array' => '部屋タイプは配列形式で送信してください。',
            'room_types.min' => '少なくとも1つの部屋タイプを選択してください。',
            'room_counts.required' => '少なくとも1つの予約枠を入力してください。',
            'room_counts.array' => '予約枠は配列形式で送信してください。',
            'room_counts.min' => '少なくとも1つの予約枠を入力してください。',
            'room_types.*.required' => '部屋タイプを選択してください。',
            'room_types.*.in' => '選択された部屋タイプは無効です。',
            'room_counts.*.required' => '予約部屋数を入力してください。',
            'room_counts.*.integer' => '予約部屋数は整数で入力してください。',
            'room_counts.*.min' => '予約部屋数は1以上である必要があります。',
        ];
    }
}
