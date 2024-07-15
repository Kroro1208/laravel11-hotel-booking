<?php

namespace App\Http\Controllers;

use App\Models\ReservationArea;

class ReservationAreaController extends Controller
{
    public function reservationArea(ReservationArea $reservationArea)
    {
        return view('backend.reservation.area', [
            'reservationArea' => $reservationArea,
        ]);
    }
}
