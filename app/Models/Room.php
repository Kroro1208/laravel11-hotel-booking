<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = ['room_type_id', 'room_number'];

    public function reservationSlots()
    {
        return $this->hasMany(ReservationSlot::class);
    }

    public function reservationDetails()
    {
        return $this->hasMany(ReservationDetail::class);
    }

    public function plans()
    {
        return $this->belongsToMany(Plan::class);
    }

    public function roomType()
    {
        return $this->belongsTo(RoomType::class);
    }
}
