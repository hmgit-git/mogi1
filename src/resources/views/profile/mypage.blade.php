@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/mypage.css') }}">
@endsection

@section('content')
<div class="mypage-container">
    <div class="user-info">
        <div class="avatar">
            <img src="{{ asset($user->profile_image ?? 'storage/AdobeStock_508578170.jpeg') }}" alt="プロフィール画像" width="120" style="border-radius: 50%;">
        </div>
        <div class="username">
            <h2>{{ $user->username }}</h2>
            <a href="{{ route('profile.edit') }}" class="edit-button">プロフィール編集</a>
        </div>
    </div>

    <div class="tabs">
        <button class="tab-button active" data-tab="listed">出品商品</button>
        <button class="tab-button" data-tab="purchased">購入商品</button>
    </div>

    <div class="tab-content active" id="listed">
        <h3>出品商品</h3>
        <div class="item-list">
            @forelse ($listedItems as $item)
            <div class="item-card">
                <img src="{{ $item->image_path }}" alt="{{ $item->name }}">
                <p>{{ $item->name }}</p>
                <p>¥{{ number_format($item->price) }}</p>
            </div>
            @empty
            <p>出品された商品はありません</p>
            @endforelse
        </div>
    </div>

    <div class="tab-content" id="purchased">
        <h3>購入商品一覧</h3>

        <div class="item-list">
            @forelse ($purchasedItems as $item)
            <div class="item-card">
                <img src="{{ $item->image_path }}" alt="{{ $item->name }}">
                <p>{{ $item->name }}</p>
                <p>¥{{ number_format($item->price) }}</p>
            </div>
            @empty
            <p>購入商品はまだありません。</p>
            @endforelse
        </div>
    </div>


</div>

<script>
    // タブ切り替えJS
    document.addEventListener('DOMContentLoaded', () => {
        const buttons = document.querySelectorAll('.tab-button');
        const tabs = document.querySelectorAll('.tab-content');

        buttons.forEach(button => {
            button.addEventListener('click', () => {
                buttons.forEach(b => b.classList.remove('active'));
                tabs.forEach(t => t.classList.remove('active'));

                button.classList.add('active');
                document.getElementById(button.dataset.tab).classList.add('active');
            });
        });
    });
</script>
@endsection