<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'capacity',
        'number_of_rooms',
        'is_active'
    ];

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    public function planRooms()
    {
        return $this->hasMany(PlanRoom::class);
    }

    public function reservationSlots()
    {
        return $this->hasMany(ReservationSlot::class);
    }
}
