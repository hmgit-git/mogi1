@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection

@section('content')
@php
$me = auth()->user();
$openConvCount = \App\Models\Conversation::forUser($me->id)->where('status','open')->count();
$unreadTotal = \App\Models\Message::whereHas('conversation', fn($q)=>$q->forUser($me->id))
->whereNull('read_at')->where('sender_id','<>',$me->id)->count();
    @endphp


    <div class="mypage-container">

        <!-- プロフィールヘッダー -->
        <div class="profile-header">
            <div class="profile-image">
                <img src="{{ asset($user->profile_image ?? 'storage/AdobeStock_508578170.jpeg') }}" alt="プロフィール画像">

                {{-- ★ 名前と評価を縦に並べる --}}
                <div class="namebox">
                    <h2 class="username">{{ $user->username }}</h2>

                    @if(($user->rating_count ?? 0) > 0)
                    @php
                    $stars = (int) ($user->rating_average_rounded ?? 0); // ← null対策
                    if ($stars < 0) $stars=0;
                        if ($stars> 5) $stars = 5;
                        @endphp
                        <div class="rating-row" aria-label="平均評価 {{ $stars }} / 5">
                            @for ($i = 1; $i <= 5; $i++)
                                <span class="star {{ $i <= $stars ? 'is-filled' : '' }}">★</span>
                                @endfor
                                <span class="rating-count">（{{ $user->rating_count }}件）</span>
                        </div>
                        @endif

                </div>
            </div>

            <div class="profile-btn">
                <a href="{{ route('profile.edit') }}" class="edit-button">プロフィール編集</a>
            </div>
        </div>

        <!-- タブメニュー -->
        <div class="tab-menu">
            <button class="tab {{ $tab === 'listed' ? 'active' : '' }}" onclick="location.href='?tab=listed'">出品した商品</button>
            <button class="tab {{ $tab === 'purchased' ? 'active' : '' }}" onclick="location.href='?tab=purchased'">購入した商品</button>
            <button class="tab {{ $tab === 'trading' ? 'active' : '' }}" onclick="location.href='?tab=trading'">
                取引中の商品 @if(($openConvCount ?? 0)>0) <span class="tab-badge tab-badge--red">{{ $openConvCount }}</span> @endif
            </button>
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

            @elseif($tab === 'trading')
            @php
            $convs = \App\Models\Conversation::forUser($me->id)
            ->where('status','open')
            ->with([
            'item:id,name,price,image_path',
            'buyer:id,name',
            'seller:id,name',
            'messages' => fn($q)=>$q->latest()->limit(1),
            ])
            ->orderByDesc('last_message_at')
            ->paginate(24);

            $unreads = \App\Models\Message::whereIn('conversation_id', $convs->pluck('id'))
            ->whereNull('read_at')->where('sender_id','<>',$me->id)
                ->selectRaw('conversation_id, COUNT(*) AS c')
                ->groupBy('conversation_id')
                ->pluck('c','conversation_id');
                @endphp

                @if($convs->count() === 0)
                <p>取引中のスレッドはありません。</p>
                @else
                <div class="item-list">
                    @foreach($convs as $conv)
                    @php
                    $partner = $me->id === $conv->buyer_id ? $conv->seller : $conv->buyer;
                    $unread = $unreads[$conv->id] ?? 0;
                    $img = $conv->item->image_path ?? null;
                    $price = number_format($conv->item->price ?? 0);
                    $last = $conv->messages->first();
                    @endphp

                    <a href="{{ route('conversations.show',$conv) }}" class="item-link">
                        <div class="item-card">
                            @if($unread > 0)
                            <span class="badge-unread">未読 {{ $unread }}</span>
                            @endif

                            @php $imgSrc = optional($conv->item)->image_url; @endphp
                            @if($imgSrc)
                            <img src="{{ $imgSrc }}" alt="{{ $conv->item->name ?? '商品' }}">
                            @else
                            <span class="noimg">NO IMAGE</span>
                            @endif

                            <p>{{ $conv->item->name ?? '商品' }}</p>
                        </div>
                    </a>

                    @endforeach
                </div>

                <div class="pagination-wrap">
                    {{ $convs->withQueryString()->links() }}
                </div>
                @endif
                @endif
        </div>


    </div>
    @endsection