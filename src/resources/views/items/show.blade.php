@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/item-detail.css') }}">
@endsection

@section('content')
<div class="item-detail">

    <!-- 左：商品画像 -->
    <div class="item-detail-left">
        <img src="{{ asset($item->image_path) }}" alt="{{ $item->name }}">
    </div>

    <!-- 右：商品情報 -->
    <div class="item-detail-right">
        <h2 class="item-name">{{ $item->name }}</h2>
        <p class="item-brand">ブランド名：{{ $item->brand ?? '未設定' }}</p>

        <p class="item-category">カテゴリ：
            @foreach ($item->categories as $category)
            <span>{{ $category->name }}</span>@if(!$loop->last), @endif
            @endforeach
        </p>

        <p class="item-price">価格：{{ number_format($item->price) }}円</p>

        <div class="reaction-row">
            <div class="reaction">
                <button id="like-button"
                    data-liked="{{ $item->likedUsers->contains(Auth::id()) ? 'true' : 'false' }}"
                    data-url="{{ route('items.like', $item->id) }}">
                    <img id="like-icon"
                        src="{{ asset('storage/images/星アイコン8.png') }}"
                        alt="いいね"
                        class="{{ $item->likedUsers->contains(Auth::id()) ? 'liked' : '' }}">
                </button>
                <div id="like-count">{{ $item->likedUsers->count() }}</div>
            </div>

            <div class="reaction">
                <img src="{{ asset('storage/images/ふきだしのアイコン.png') }}" alt="コメント数">
                <div>{{ $item->comments->count() }}</div>
            </div>
        </div>

        @if (Auth::check() && $item->user_id !== Auth::id() && !$item->is_sold)
        <div class="purchase-btn">
            <a href="{{ route('purchase.show', ['item' => $item->id]) }}">
                <button class="full-width-button red">購入手続きへ</button>
            </a>
        </div>
        @endif


        <div class="item-description">
            <h3>商品説明</h3>
            <p>{{ $item->description }}</p>
            <p>状態: {{ $item->condition }}</p>
        </div>

        <div class="item-meta">
            <h3>商品情報</h3>
            <ul>
                <li>
                    <strong>カテゴリー:</strong>
                    <div class="item-categories">
                        @foreach ($item->categories as $category)
                        <span class="category-tag">{{ $category->name }}</span>
                        @endforeach
                    </div>
                </li>
                <li>
                    <strong>状態:</strong> {{ $item->condition }}
                </li>
            </ul>
        </div>




        <div class="item-comments">
            <h3>コメント（{{ $item->comments->count() }}）</h3>

            @forelse ($item->comments as $comment)
            <div class="comment">
                <strong>{{ $comment->user->name ?? '匿名ユーザー' }}</strong><br>
                {{ $comment->content }}
            </div>
            @empty
            <p>コメントはまだありません</p>
            @endforelse

            <form action="{{ route('comments.store', $item->id) }}" method="POST" novalidate>
                @csrf
                <textarea name="content" rows="3" placeholder="コメントを入力">{{ old('content') }}</textarea>

                @error('content')
                <div class="error-message">{{ $message }}</div>
                @enderror

                @auth
                <button type="submit" class="full-width-button red">コメントを送信する</button>
                @else
                <a href="{{ route('login') }}" class="full-width-button red" style="display: inline-block; text-align: center;">
                    ログインしてコメントする
                </a>
                @endauth
            </form>
        </div>

    </div>

</div>
@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const likeButton = document.getElementById('like-button');
        if (!likeButton) return;

        likeButton.addEventListener('click', function() {
            const icon = document.getElementById('like-icon');
            const count = document.getElementById('like-count');
            const url = this.dataset.url;

            fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                })
                .then(response => {
                    if (!response.ok) {
                        if (response.status === 401) {
                            window.location.href = '/login';
                        } else {
                            throw new Error('エラー発生');
                        }
                    }
                    return response.json();
                })
                .then(data => {
                    console.log(data); // ← ここで中身を確認！
                    icon.classList.toggle('liked', data.liked);
                    count.textContent = data.likes_count;
                })
                .catch(error => {
                    console.error(error);
                    alert('通信エラーが発生しました');
                });
        });
    });
</script>
@endsection

@endsection