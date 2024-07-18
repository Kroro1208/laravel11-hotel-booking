<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Plan extends Model

{
    use HasFactory;

    protected $fillable = ['title', 'description', 'image', 'start_date', 'end_date', 'is_reserved'];
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_reserved' => 'boolean',
    ];


    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function planRooms()
    {
        return $this->hasMany(PlanRoom::class);
    }

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    public function updateReservationStatus()
    {
        $allSlotsUnavailable = $this->reservationSlots()
            ->where('status', '!=', 'unavailable')
            ->doesntExist();

        $this->is_reserved = $allSlotsUnavailable;
        $this->save();
    }
}
