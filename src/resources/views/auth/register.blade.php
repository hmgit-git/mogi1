@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endsection

@section('content')
<div class="auth-form-container">
    <h2>会員登録</h2>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div>
            <label for="username">ユーザー名</label>
            <input type="text" name="username" id="username" value="{{ old('username') }}">

            @error('username')
            <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label for="email">メールアドレス</label>
            <input type="text" name="email" id="email" value="{{ old('email') }}">
            @error('email')
            <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label for="password">パスワード</label>
            <input type="password" name="password" id="password">

            @error('password')
            <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label for="password_confirmation">確認用パスワード</label>
            <input type="password" name="password_confirmation" id="password_confirmation">

            @error('password_confirmation')
            <div class="error-message">{{ $message }}</div>
            @enderror
        </div>


        <button type="submit">登録する</button>
    </form>

    <div class="link-text">
        <a href="{{ route('logout-and-go-login') }}" class="link-text__a">ログインはこちら</a>

    </div>
</div>
@endsection