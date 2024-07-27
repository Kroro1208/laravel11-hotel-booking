<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Plan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'images',
        'start_date',
        'end_date',
        'is_reserved',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'images' => 'array',
        'is_reserved' => 'boolean',
    ];

    public function planRooms()
    {
        return $this->hasMany(PlanRoom::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function reservationSlots()
    {
        return $this->hasMany(ReservationSlot::class);
    }

    public function roomTypes(): BelongsToMany
    {
        return $this->belongsToMany(RoomType::class, 'plan_rooms')
            ->withPivot('room_count')
            ->withTimestamps();
    }
}
