<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class AdminController extends Controller
{
    public function AdminDashboard(): View
    {
        return view('admin.index');
    }
}
