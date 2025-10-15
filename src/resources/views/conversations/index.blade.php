@extends('layouts.app')
@section('title','取引中の商品')

@section('css')
<link rel="stylesheet" href="{{ asset('css/conversations.css') }}">
@endsection

@section('content')
<div class="conv-grid">

    @forelse($list as $conv)
    @php
    // コントローラ側で with(['item','buyer','seller','messages' => fn($q)=>$q->latest()->limit(1)]) を付けている想定
    $last = $conv->messages->first();
    $partner = auth()->id() === $conv->buyer_id ? $conv->seller : $conv->buyer;
    $unread = $unreads[$conv->id] ?? 0;
    $img = $conv->item->image_path ?? null; // 例: storage/items/xxx.jpg
    $price = number_format($conv->item->price ?? 0);
    @endphp

    <a href="{{ route('conversations.show',$conv) }}" class="conv-card">
        {{-- 未読バッジ（左上） --}}
        @if($unread > 0)
        <span class="badge-unread">未読 {{ $unread }}</span>
        @endif

        {{-- サムネイル --}}
        <div class="conv-thumb">
            @if($img)
            <img src="{{ asset('storage/'.$img) }}" alt="item">
            @else
            <span style="font-size:12px;color:#9ca3af;">NO IMAGE</span>
            @endif
        </div>

        {{-- 本文 --}}
        <div class="conv-body">
            <div class="conv-title">{{ $conv->item->name ?? '商品' }}</div>
            <div class="conv-meta">
                <span>相手: {{ $partner->name }}</span>
                <span>¥{{ $price }}</span>
            </div>
            @if($last)
            <div class="conv-last">最後: {{ \Illuminate\Support\Str::limit($last->body, 28) }}</div>
            @endif
        </div>
    </a>

    @empty
    <div style="grid-column: 1 / -1; text-align:center; color:#666;">
        取引中のスレッドはありません。
    </div>
    @endforelse

</div>

<div style="max-width:1100px;margin:8px auto 24px;">
    {{ $list->links() }}
</div>
@endsection