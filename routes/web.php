<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\PlanController as AdminPlanController;
use App\Http\Controllers\User\PlanController as UserPlanController;
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
// ユーザー用プラン
Route::get('user/plan/{plan}', [UserPlanController::class, 'show'])->name('user.plan.show');


// 管理者用
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'adminDashboard'])->name('admin.dashboard');
    Route::get('/admin/logout', [AdminController::class, 'adminLogout'])->name('admin.logout');
    Route::get('/admin/profile', [AdminController::class, 'adminProfile'])->name('admin.profile');
    Route::post('/admin/profile/store', [AdminController::class, 'adminProfileStore'])->name('admin.profile.store');
    Route::get('/admin/password/edit', [AdminController::class, 'adminPasswordEdit'])->name('admin.password.edit');
    Route::post('/admin/password/update', [AdminController::class, 'adminPasswordUpdate'])->name('admin.password.update');

    // プラン作成用
    Route::get('/plan/index', [AdminPlanController::class, 'index'])->name('plan.index');
    Route::get('/plan/create', [AdminPlanController::class, 'create'])->name('plan.create');
    Route::post('/plan/store', [AdminPlanController::class, 'store'])->name('plan.store');
    Route::get('/plan/{plan}', [AdminPlanController::class, 'show'])->name('plan.show');
    Route::get('/plan/{plan}/edit', [AdminPlanController::class, 'edit'])->name('plan.edit');
    Route::patch('/plan/{plan}', [AdminPlanController::class, 'update'])->name('plan.update');
    Route::delete('/plan/{plan}', [AdminPlanController::class, 'destroy'])->name('plan.destroy');
});

Route::get('/admin/login', [AdminController::class, 'AdminLogin'])->name('admin.login');


require __DIR__ . '/auth.php';
