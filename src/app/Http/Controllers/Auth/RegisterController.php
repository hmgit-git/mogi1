<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller; 
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;

class RegisterController extends Controller
{
    public function show()
    {
        return view('auth.register');
    }

    public function store(RegisterRequest $request)
    {
        $user = User::create([
            'username' => $request->username,
            'name' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            // email_verified_at は入れない！本番仕様に
        ]);

        // ログイン状態にする
        auth()->login($user);

        // 認証メールを送信！
        event(new Registered($user));

        return redirect()->route('mypage.profile');
    }
}
