<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ReservationSlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'plan_id',
        'room_type_id',
        'date',
        'total_rooms',
        'booked_rooms',
        'status',
    ];
    protected $casts = [
        'date' => 'date',
        'total_rooms' => 'integer',
        'booked_rooms' => 'integer',
    ];

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function roomType()
    {
        return $this->belongsTo(RoomType::class);
    }

    // 状態の定数を定義
    public const STATUS_AVAILABLE = 'available';
    public const STATUS_FEW = 'few';
    public const STATUS_UNAVAILABLE = 'unavailable';

    public static function getStatusOptions()
    {
        return [
            self::STATUS_AVAILABLE => '○',
            self::STATUS_FEW => '△',
            self::STATUS_UNAVAILABLE => '×',
        ];
    }

    public function getAvailableRoomsForDate($date)
    {
        if ($date >= $this->date && $date <= $this->plan->end_date) {
            return $this->total_rooms - $this->booked_rooms;
        }
        return 0;
    }

    public function updateStatus()
    {
        $planRoom = $this->plan->planRooms()->where('room_type_id', $this->room_type_id)->first();

        if (!$planRoom) {
            throw new \Exception('関連する PlanRoom が見つかりません。');
        }

        $availableRooms = $planRoom->room_count - $this->booked_rooms;
        $availablePercentage = ($availableRooms / $planRoom->room_count) * 100;

        if ($availableRooms <= 0) {
            $this->status = self::STATUS_UNAVAILABLE;
        } elseif ($availablePercentage <= 30) {
            $this->status = self::STATUS_FEW;
        } else {
            $this->status = self::STATUS_AVAILABLE;
        }

        $this->save();

        // プランの予約状態を更新
        $this->plan->updateReservationStatus();
    }

    public function book($roomCount = 1)
    {
        $planRoom = $this->plan->planRooms()->where('room_type_id', $this->room_type_id)->first();

        if (!$planRoom) {
            throw new \Exception('関連する PlanRoom が見つかりません。');
        }

        if ($this->booked_rooms + $roomCount <= $planRoom->room_count) {
            DB::transaction(function () use ($roomCount) {
                $this->booked_rooms += $roomCount;
                $this->updateStatus();
            });
            return true;
        }
        return false;
    }

    public function cancelBooking($roomCount = 1)
    {
        if ($this->booked_rooms - $roomCount >= 0) {
            DB::transaction(function () use ($roomCount) {
                $this->booked_rooms -= $roomCount;
                $this->updateStatus();
            });
            return true;
        }
        return false;
    }

    public function getAvailableRooms()
    {
        $planRoom = $this->plan->planRooms()->where('room_type_id', $this->room_type_id)->first();

        if (!$planRoom) {
            throw new \Exception('関連する PlanRoom が見つかりません。');
        }

        return $planRoom->room_count - $this->booked_rooms;
    }
}
