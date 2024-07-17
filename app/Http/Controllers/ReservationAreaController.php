<?php

namespace App\Http\Controllers;

use App\Models\ReservationArea;
use Illuminate\Http\Request;

class ReservationAreaController extends Controller
{
    public function reservationArea(ReservationArea $reservationArea)
    {
        return view('backend.reservation.area', [
            'reservationArea' => $reservationArea,
        ]);
    }

    public function update(Request $request)
    {
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = date('YmdHi') . '_' . $file->getClientOriginalName();
            $file->move(public_path('upload/user_images'), $filename);

            return to_route('user.profile')->with('success', 'プランが更新されました');
        }
    }
}
