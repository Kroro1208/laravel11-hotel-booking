<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ReservationAreaController;
use App\Http\Controllers\UserController;

use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [UserController::class, 'index'])->name('user.index');

Route::get('/dashboard', function () {
    return view('frontend.dashboard.user_dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [UserController::class, 'userProfile'])->name('user.profile');
    Route::patch('/profile/update', [UserController::class, 'userProfileUpdate'])->name('user.profile.update');
    Route::get('/user/logout', [UserController::class, 'userLogout'])->name('user.logout');
    Route::get('/user/password/edit', [UserController::class, 'userPasswordEdit'])->name('user.password.edit');
    Route::patch('/user/password/update', [UserController::class, 'userPasswordUpdate'])->name('user.password.update');
});

require __DIR__ . '/auth.php';

// 管理者用
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'adminDashboard'])->name('admin.dashboard');
    Route::get('/admin/logout', [AdminController::class, 'adminLogout'])->name('admin.logout');
    Route::get('/admin/profile', [AdminController::class, 'adminProfile'])->name('admin.profile');
    Route::post('/admin/profile/store', [AdminController::class, 'adminProfileStore'])->name('admin.profile.store');
    Route::get('/admin/password/edit', [AdminController::class, 'adminPasswordEdit'])->name('admin.password.edit');
    Route::post('/admin/password/update', [AdminController::class, 'adminPasswordUpdate'])->name('admin.password.update');
});

Route::get('/admin/login', [AdminController::class, 'adminLogin'])->name('admin.login');

Route::get('/reservation/area', [ReservationAreaController::class, 'reservationArea'])->name('reservation.area');