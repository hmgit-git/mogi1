@extends('layouts.app')

@section('content')
<div class="tab-buttons">
    <a href="{{ route('items.index') }}" class="{{ request('tab') !== 'mylist' ? 'active' : '' }}">おすすめ</a>
    <a href="{{ route('items.index', ['tab' => 'mylist']) }}" class="{{ request('tab') === 'mylist' ? 'active' : '' }}">マイリスト</a>
</div>
<hr class="tab-divider">

<h1>{{ request('tab') === 'mylist' ? 'マイリスト' : 'おすすめ商品' }}</h1>

<div class="item-list">
    @foreach ($items as $item)
    <a href="{{ route('items.show', $item->id) }}" class="item-link">
        <div class="item-card">
            <img src="{{ $item->image_path }}" alt="{{ $item->name }}">
            <p>{{ $item->name }}</p>
            <p>¥{{ number_format($item->price) }}</p>

            @if ($item->is_sold)
            <span class="sold-badge">sold</span>
            @endif
        </div>
    </a>
    @endforeach
</div>
@endsection