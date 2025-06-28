@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection

@section('content')
<div class="profile-container">
    <h2>プロフィール設定</h2>

    <form method="POST" action="#" enctype="multipart/form-data">
        @csrf

        <div class="profile-image-section">
            <div class="image-preview">
                <img src="{{ Auth::user()->profile_image ?? asset('img/default.png') }}" alt="プロフィール画像">
            </div>

            <label class="upload-button" for="profile_image">画像を選択する</label>
            <input type="file" name="profile_image" id="profile_image" class="file-input-hidden">
        </div>


        <div class="form-group">
            <label for="username">ユーザー名</label>
            <input type="text" name="username" id="username" value="{{ old('username', Auth::user()->username) }}">
        </div>

        <div class="form-group">
            <label for="zip">郵便番号</label>
            <input type="text" name="zip" id="zip" value="{{ old('zip', Auth::user()->zip) }}">
        </div>

        <div class="form-group">
            <label for="address">住所</label>
            <input type="text" name="address" id="address" value="{{ old('address', Auth::user()->address) }}">
        </div>

        <div class="form-group">
            <label for="building">建物名</label>
            <input type="text" name="building" id="building" value="{{ old('building', Auth::user()->building) }}">
        </div>

        <button type="submit" class="submit-button">更新する</button>
    </form>
</div>
@endsection