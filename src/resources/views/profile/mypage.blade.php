@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection

@section('content')
<div class="mypage-container">

    <!-- プロフィールヘッダー -->
    <div class="profile-header">
        <div class="profile-image">
            <img src="{{ asset($user->profile_image ?? 'storage/AdobeStock_508578170.jpeg') }}" alt="プロフィール画像">
            <h2 class="username">{{ $user->username }}</h2>
        </div>
        <div class="profile-btn">
            <a href="{{ route('profile.edit') }}" class="edit-button">プロフィール編集</a>
        </div>
    </div>

    <!-- タブメニュー -->
    <div class="tab-menu">
        <button class="tab {{ $tab === 'listed' ? 'active' : '' }}" onclick="location.href='?tab=listed'">出品した商品</button>
        <button class="tab {{ $tab === 'purchased' ? 'active' : '' }}" onclick="location.href='?tab=purchased'">購入した商品</button>
    </div>

    <!-- 商品一覧 -->
    <div class="item-list-wrapper">
        @if($tab === 'listed')
        @if($listedItems->isEmpty())
        <p>出品した商品はまだありません。</p>
        @else
        @include('items.partials.list', ['items' => $listedItems])
        @endif
        @elseif($tab === 'purchased')
        @if($purchasedItems->isEmpty())
        <p>購入した商品はまだありません。</p>
        @else
        @include('items.partials.list', ['items' => $purchasedItems])
        @endif
        @endif
    </div>

</div>
@endsection