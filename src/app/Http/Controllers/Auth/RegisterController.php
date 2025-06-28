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
        // 登録処理して、$user に代入！
        $user = User::create([
            'username' => $request->username,
            'name' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // ログイン状態にして
        auth()->login($user);

        // 認証メールを送信！
        $user->sendEmailVerificationNotification();

        // 編集画面へリダイレクト！
        return redirect('/profile/edit');
    }
}
