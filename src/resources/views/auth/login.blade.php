@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endsection


@section('content')
<div class="auth-form-container">
    <h2>ログイン</h2>

    @if (session('status'))
    <div class="status-message">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}" novalidate>

        @csrf

        <div>
            <label for="email">メールアドレス</label>
            <input type="email" name="email" value="{{ old('email') }}">
            @error('email')
            <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label for="password">パスワード</label>
            <input type="password" name="password">
            @error('password')
            <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit">ログイン</button>
    </form>

    {{-- 仮スタイル：登録リンク --}}
    <div class="link-text">
        <a href="{{ route('register') }}" class="link-text__a">登録はこちら</a>
    </div>
</div>
@endsection