<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'image', 'price', 'start_date', 'end_date', 'is_reserved'];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_reserved' => 'boolean',
    ];

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function planRooms(): HasMany
    {
        return $this->hasMany(PlanRoom::class);
    }

    public function rooms(): BelongsToMany
    {
        return $this->belongsToMany(Room::class);
    }

    public function reservationSlots(): HasMany
    {
        return $this->hasMany(ReservationSlot::class);
    }

    public function updateReservationStatus(): void
    {
        $this->is_reserved = !$this->reservationSlots()
            ->where('status', '!=', 'unavailable')
            ->exists();
        $this->save();
    }

    public function getAvailabilityForDateRange($startDate, $endDate): array
    {
        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate);

        $reservationSlots = $this->reservationSlots()
            ->with('roomType')
            ->whereBetween('date', [$startDate, $endDate])
            ->get()
            ->groupBy(['date', 'room_type_id']);

        $availability = [];
        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $dateString = $currentDate->format('Y-m-d');
            $dayAvailability = [];

            foreach ($this->planRooms as $planRoom) {
                $roomTypeName = $planRoom->roomType->name;
                $slot = $reservationSlots[$dateString][$planRoom->room_type_id] ?? null;

                $availableRooms = $slot
                    ? $slot->first()->total_rooms - $slot->first()->booked_rooms
                    : $planRoom->room_count;

                $dayAvailability[$roomTypeName] = $availableRooms;
            }

            $availability[$dateString] = $dayAvailability;
            $currentDate->addDay();
        }

        return $availability;
    }
}
