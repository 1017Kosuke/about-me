<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        $previousProfileFilePath = $user->profile_path;

        // 🔹 ファイルがアップロードされた場合のみ処理
        if ($request->hasFile('profile_photo')) {
            // publicディスクに保存 (storage/app/public/profile_photos/)
            $path = $request->file('profile_photo')->store('profile_photos', 'public');

            // 古い画像を削除（存在する場合のみ）
            if ($previousProfileFilePath && Storage::disk('public')->exists($previousProfileFilePath)) {
                Storage::disk('public')->delete($previousProfileFilePath);
            }

            // 新しいパスを代入
            $user->profile_path = $path;
        }

        // 🔹 他の項目を更新
        $user->fill([
            'username' => $validated['username'],
            'email' => $validated['email'],
        ]);

        // メール変更時は認証リセット
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
