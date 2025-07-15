@extends('layouts.app')

@section('content')
<div class="tab-buttons">
    <a href="{{ route('items.index', ['keyword' => request('keyword')]) }}"
        class="{{ request('tab') !== 'mylist' ? 'active' : '' }}">おすすめ</a>

    <a href="{{ route('items.index', ['tab' => 'mylist', 'keyword' => request('keyword')]) }}"
        class="{{ request('tab') === 'mylist' ? 'active' : '' }}">マイリスト</a>
</div>


<hr class="tab-divider">

<h1 class="item-list-title">{{ request('tab') === 'mylist' ? 'マイリスト' : 'おすすめ商品' }}</h1>

<div class="item-list">
    @if(request('tab') === 'mylist' && !Auth::check())
    {{-- 何も表示しない --}}
    @else
    @forelse ($items as $item)
    <a href="{{ route('items.show', $item->id) }}" class="item-link">
        <div class="item-card">
            <img src="{{ asset($item->image_path) }}" alt="{{ $item->name }}">
            <p>{{ $item->name }}</p>

            @if ($item->is_sold)
            <span class="sold-badge">Sold</span>
            @endif
        </div>
    </a>
    @empty
    <p>表示できる商品がありません。</p>
    @endforelse
    @endif
</div>

@endsection