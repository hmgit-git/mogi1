@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection


@section('content')
<div class="profile-container">
    <h2>プロフィール設定</h2>

    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" novalidate>
        @csrf

        <div class="profile-image-section">
            <div class="image-preview">
                <img src="{{ asset($user->profile_image) }}" alt="プロフィール画像">
            </div>
            <label class="upload-button">
                画像を選択する
                <input type="file" name="profile_image" class="file-input-hidden">
            </label>
            @error('profile_image')
            <div style="color: red;">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label>ユーザー名</label>
            <input type="text" name="username" value="{{ old('username', $user->username) }}">
            @error('username')
            <div style="color: red;">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label>郵便番号</label>
            <input type="text" name="zip" value="{{ old('zip', $user->zip) }}">
            @error('zip')
            <div style="color: red;">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label>住所</label>
            <input type="text" name="address" value="{{ old('address', $user->address) }}">
            @error('address')
            <div style="color: red;">{{ $message }}</div>
            @enderror

        </div>

        <div class="form-group">
            <label>建物名</label>
            <input type="text" name="building" value="{{ old('building', $user->building) }}">
        </div>

        <button type="submit" class="submit-button">更新する</button>
    </form>
</div>
@endsection