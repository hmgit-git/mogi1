@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/verify-email.css') }}">
@endsection

@section('content')
<div class="verify-email-container">
    <p>登録していただいたメールアドレスに認証メールを送信しました。<br>メール認証を完了してください。</p>

    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit" class="verify-button">認証はこちら</button>
    </form>

    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit" class="resend-link">認証メールを再送する</button>
    </form>

    @if (session('message'))
    <p class="message">{{ session('message') }}</p>
    @endif
</div>
@endsection