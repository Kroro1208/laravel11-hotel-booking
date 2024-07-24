<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ReservationSlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_type_id',
        'date',
        'available_rooms',
        'booked_rooms',
        'price'
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function roomType()
    {
        return $this->belongsTo(RoomType::class);
    }

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
            return $this->getAvailableRooms();
        }
        return 0;
    }

    public function updateStatus()
    {
        $planRoom = $this->plan->planRooms()->where('room_type_id', $this->room_type_id)->first();

        if (!$planRoom) {
            throw new \Exception('関連する PlanRoom が見つかりません。');
        }

        $availableRooms = $this->getAvailableRooms();
        $availablePercentage = ($availableRooms / $planRoom->room_count) * 100;

        if ($availableRooms <= 0) {
            $this->status = self::STATUS_UNAVAILABLE;
        } elseif ($availablePercentage <= 30) {
            $this->status = self::STATUS_FEW;
        } else {
            $this->status = self::STATUS_AVAILABLE;
        }

        $this->save();

        $this->plan->updateReservationStatus();
    }

    public function book($roomCount = 1)
    {
        if ($roomCount < 1) {
            throw new \InvalidArgumentException('予約する部屋数は1以上である必要があります。');
        }

        $availableRooms = $this->getAvailableRooms();

        if ($availableRooms < $roomCount) {
            throw new \Exception("予約可能な部屋数が不足しています。予約可能数: {$availableRooms}, 要求数: {$roomCount}");
        }

        DB::transaction(function () use ($roomCount) {
            $this->booked_rooms += $roomCount;
            $this->save();
            $this->updateStatus();
        });

        return true;
    }

    public function cancelBooking($roomCount = 1)
    {
        if ($roomCount < 1) {
            throw new \InvalidArgumentException('キャンセルする部屋数は1以上である必要があります。');
        }

        if ($this->booked_rooms < $roomCount) {
            throw new \Exception("キャンセル可能な部屋数が不足しています。予約済数: {$this->booked_rooms}, 要求数: {$roomCount}");
        }

        DB::transaction(function () use ($roomCount) {
            $this->booked_rooms -= $roomCount;
            $this->save();
            $this->updateStatus();
        });

        return true;
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
