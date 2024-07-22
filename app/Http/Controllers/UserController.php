<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserProfileUpdateRequest;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    // ログインしていないユーザーにも見える
    public function index(): View
    {
        $plans = Plan::with(['planRooms.roomType', 'reservations'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('frontend.index', [
            'plans' => $plans
        ]);
    }

    public function userProfile()
    {
        $id = Auth::user()->id;
        $profileData = User::find($id);

        return view('frontend.dashboard.edit_profile', [
            'profileData' => $profileData,
        ]);
    }

    public function userProfileUpdate(UserProfileUpdateRequest $request): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $validatedData = $request->validated();

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = date('YmdHi') . '_' . $file->getClientOriginalName();
            $file->move(public_path('upload/user_images'), $filename);

            // 古い画像を削除
            if ($user->photo) {
                $oldPhotoPath = public_path('upload/user_images/' . $user->photo);
                if (file_exists($oldPhotoPath)) {
                    unlink($oldPhotoPath);
                }
            }
            $validatedData['photo'] = $filename;
        }

        $user->update($validatedData);

        return to_route('user.profile')->with('success', 'プロフィールが更新されました');
    }

    public function userLogout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $notification = [
            'message' => 'ログアウトに成功しました',
            'alert-type' => 'success',
        ];

        return to_route('login')->with($notification);
    }

    public function userPasswordEdit()
    {
        $id = Auth::user()->id;
        $profileData = User::find($id);

        return view('frontend.dashboard.password_edit', [
            'profileData' => $profileData,
        ]);
    }

    public function userPasswordUpdate(Request $request): RedirectResponse
    {
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|confirmed',
        ]);

        if (!Hash::check($request->old_password, auth::user()->password)) {
            $notification = [
                'message' => 'パスワードが一致しません',
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
