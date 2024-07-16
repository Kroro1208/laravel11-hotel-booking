<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    public function reservationSlots()
    {
        return $this->hasMany(ReservationSlot::class);
    }

    public function reservationDetails()
    {
        return $this->hasMany(ReservationDetail::class);
    }
}
