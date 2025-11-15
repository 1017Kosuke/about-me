<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // 入力データの検証
        $request->validate([
            'username' => ['required', 'string', 'max:255','unique:' . User::class],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'description' => ['required', 'string', 'max:2000'],
            'profile_picture' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:10240'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // デフォルトは null
        $profilePicturePath = null;

        // プロフィール画像がアップロードされていたら保存処理
        if ($request->hasFile('profile_picture')) {
            $file = $request->file('profile_picture');
            $md5Filename = md5(
                $file->getClientOriginalName() .
                $request->username .
                now()->toDateString()
            ) . '.' . $file->getClientOriginalExtension();

            $profilePicturePath = $file->storeAs(
                'users/profiles/profile_pictures',
                $md5Filename,
                'public'
            );
        }

        // ユーザーの作成
        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'description' => $request->description,
            'profile_path' => $profilePicturePath,
            'password' => Hash::make($request->password),
        ]);

        // ユーザーのログイン
        event(new Registered($user));
        Auth::login($user);

        // ダッシュボードへリダイレクト
        return redirect(route('dashboard', absolute: false));
    }
}
