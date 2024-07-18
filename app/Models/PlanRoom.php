<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanRoom extends Model
{
    use HasFactory;
    protected $fillable = [
        'plan_id', 'room_type_id', 'room_count',
    ];

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function roomType()
    {
        return $this->belongsTo(RoomType::class);
    }
}
