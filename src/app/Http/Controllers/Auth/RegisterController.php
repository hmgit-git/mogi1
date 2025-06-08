<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller; 
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;

class RegisterController extends Controller
{
    public function show()
    {
        return view('auth.register');
    }

    public function store(RegisterRequest $request)
    {
        // 登録処理
        User::create([
            'username' => $request->username,
            'name' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'email_verified_at' => Carbon::now(), // ← 本番ではここだけ削除！
        ]);

        // ログイン状態にしてリダイレクト
        auth()->attempt($request->only('email', 'password'));

        return redirect('/profile/edit');
    }
}
