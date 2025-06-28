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
    <div class="item-card">
        <a href="{{ route('items.show', $item->id) }}" style="text-decoration: none; color: inherit;">
            <img src="{{ $item->image_path }}" alt="{{ $item->name }}" width="150">
            <h3>{{ $item->name }}</h3>
            @if ($item->is_sold)
            <span style="color: red; font-weight: bold;">Sold</span>
            @endif
        </a>
    </div>
    @endforeach
</div>
@endsection