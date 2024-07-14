<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function AdminDashboard(): View
    {
        return view('admin.index');
    }

    public function AdminLogout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/admin/login');
    }

    public function AdminLogin(): View
    {
        return view('admin.login');
    }

    public function AdminProfile()
    {
        $id = Auth::user()->id;
        $profileData = User::find($id);

        return view('admin.profile', [
            'profileData' => $profileData,
        ]);
    }

    public function AdminProfileStore(ProfileUpdateRequest $request): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $validatedData = $request->validated();

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = date('YmdHi') . '_' . $file->getClientOriginalName();
            $file->move(public_path('upload/admin_images'), $filename);

            // 古い画像を削除
            if ($user->photo) {
                $oldPhotoPath = public_path('upload/admin_images/' . $user->photo);
                if (file_exists($oldPhotoPath)) {
                    unlink($oldPhotoPath);
                }
            }

            $validatedData['photo'] = $filename;
        }

        $user->update($validatedData);

        return to_route('admin.profile')->with('success', 'プロフィールが更新されました');
    }
}
