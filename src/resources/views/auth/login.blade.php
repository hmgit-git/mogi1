@extends('layouts.app')

@section('content')
<div class="login-container">
    <h2>ログイン</h2>

    @if (session('status'))
    <div class="status-message">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="form-group">
            <label for="email">メールアドレス</label>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus>
            @error('email')
            <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="password">パスワード</label>
            <input type="password" name="password" required>
            @error('password')
            <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit">ログイン</button>
    </form>

    {{-- 仮スタイル：登録リンク --}}
    <div class="register-link">
        <p>アカウントをお持ちでない方は</p>
        <a href="{{ route('register') }}">登録はこちら</a>
    </div>
</div>
@endsection