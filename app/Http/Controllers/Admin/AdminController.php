<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function adminDashboard(): View
    {
        return view('admin.index');
    }

    public function adminLogout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/admin/login');
    }

    public function adminLogin(): View
    {
        return view('admin.login');
    }

    public function adminProfile()
    {
        $id = Auth::user()->id;
        $profileData = User::find($id);

        return view('admin.profile', [
            'profileData' => $profileData,
        ]);
    }

    public function adminProfileStore(ProfileUpdateRequest $request): RedirectResponse
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

    public function adminPasswordEdit()
    {
        $id = Auth::user()->id;
        $profileData = User::find($id);

        return view('admin.password_edit', [
            'profileData' => $profileData,
        ]);
    }

    public function adminPasswordUpdate(Request $request): RedirectResponse
    {
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|confirmed',
        ]);

        if (!Hash::check($request->old_password, auth::user()->password)) {
            $notification = [
                'message' => 'パスワードが致しません',
                'alert-type' => 'error',
            ];

            return back()->with($notification);
        }

        User::whereId(auth::user()->id)->update([
            'password' => Hash::make($request->new_password),
        ]);

        $notification = [
            'message' => 'パスワードが更新されました',
            'alert-type' => 'success',
        ];

        return back()->with($notification);
    }
}
