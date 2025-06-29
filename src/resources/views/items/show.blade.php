@extends('layouts.app')

@section('content')
<h2>{{ $item->name }}</h2>
<img src="{{ $item->image_path }}" alt="{{ $item->name }}" width="300">

<p><strong>ブランド名：</strong>{{ $item->brand ?? '未設定' }}</p>
<p><strong>カテゴリ：</strong>
    @foreach ($item->categories as $category)
    <span>{{ $category->name }}</span>@if(!$loop->last), @endif
    @endforeach
</p>
<p><strong>価格：</strong>{{ number_format($item->price) }}円</p>

<div style="display: flex; gap: 24px; align-items: center; margin-bottom: 4px;">
    <!-- いいね -->
    <div style="text-align: center;">
        <button id="like-button"
            data-liked="{{ $item->likedUsers->contains(Auth::id()) ? 'true' : 'false' }}"
            data-url="{{ route('items.like', $item->id) }}"
            style="border: none; background: none; font-size: 24px; cursor: pointer;">
            <span id="like-icon" style="font-size: 24px;">
                {{ $item->likedUsers->contains(Auth::id()) ? '★' : '☆' }}
            </span>

        </button>
        <div id="like-count">{{ $item->likedUsers->count() }}</div>
    </div>

    <!-- コメント -->
    <div style="text-align: center;">
        <div style="font-size: 24px;">💬</div>
        <div>{{ $item->comments->count() }}</div>
    </div>
</div>

<!-- 購入ボタン -->
<div style="margin-top: 20px;">
    <a href="{{ route('purchase.show', ['item' => $item->id]) }}">
        <button>
            購入手続きへ
        </button>
    </a>
</div>




<h3>商品説明</h3>
<p>{{ $item->description }}</p>

<h3>商品情報</h3>
<ul>
    <li>状態: {{ $item->condition }}</li>
</ul>

<h3>コメント一覧</h3>
@forelse ($item->comments as $comment)
<div style="margin-bottom: 10px;">
    <strong>{{ $comment->user->name ?? '匿名ユーザー' }}</strong><br>
    {{ $comment->content }}
</div>
@empty
<p>コメントはまだありません</p>
@endforelse

@if (Auth::check())
<form action="{{ route('comments.store', $item->id) }}" method="POST" novalidate>
    @csrf
    <textarea name="content" rows="3" placeholder="コメントを入力" required maxlength="255" style="width: 100%;"></textarea>

    @error('content')
    <div style="color: red;">{{ $message }}</div>
    @enderror
    <button type="submit">コメントを送信する</button>
</form>
@else
<p>※コメント投稿にはログインが必要です。</p>
@endif

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
                            // 未ログインならログインページへ
                            window.location.href = '/login';
                        } else {
                            throw new Error('予期しないエラー');
                        }
                    }
                    return response.json();
                })
                .then(data => {
                    icon.textContent = data.liked ? '★' : '☆';
                    count.textContent = `${data.likes_count}`;
                })
                .catch(error => {
                    console.error('エラー:', error);
                    alert('通信エラーが発生しました');
                });
        });
    });
</script>

@endsection