<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReservationSlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'date',
        'price',
        'status',
    ];

    // 状態の定数を定義
    public const STATUS_AVAILABLE = 'available';
    public const STATUS_FEW = 'few';
    public const STATUS_UNAVAILABLE = 'unavailable';

    // 状態の配列を提供するメソッド
    public static function getStatusOptions()
    {
        return [
            self::STATUS_AVAILABLE => '◎',
            self::STATUS_FEW => '△',
            self::STATUS_UNAVAILABLE => '×',
        ];
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
