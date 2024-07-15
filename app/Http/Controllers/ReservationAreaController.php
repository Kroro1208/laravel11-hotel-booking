<?php

namespace App\Http\Controllers;

use App\Models\ReservationArea;
use Illuminate\Http\Request;

class ReservationAreaController extends Controller
{
    public function reservationArea(ReservationArea $reservationArea)
    {
        return view('backend.reservation.area', [
            'reservationArea' => $reservationArea
        ]);
    }
}
