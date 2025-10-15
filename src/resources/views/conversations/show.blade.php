@extends('layouts.app')
@section('title','取引チャット')

@section('css')
<link rel="stylesheet" href="{{ asset('css/conversations.css') }}">
@endsection

@section('content')
@php
$me = auth()->user();
$isBuyer = $me->id === $conversation->buyer_id;
$isOpen = $conversation->status === 'open';
$partner = $isBuyer ? $conversation->seller : $conversation->buyer;
$itemName = $conversation->item->name ?? '商品';
$itemPrice= number_format($conversation->item->price ?? 0);
$imgUrl = optional($conversation->item)->image_url;
@endphp

<div class="conv-wrap">

    {{-- 左：その他の取引 --}}
    <aside class="panel side">
        <h2>その他の取引</h2>
        @php
        $myConvs = \App\Models\Conversation::forUser($me->id)
        ->where('status','open')->orderByDesc('last_message_at')
        ->with(['item','buyer','seller'])->get();

        $unreadsSide = \App\Models\Message::whereIn('conversation_id', $myConvs->pluck('id'))
        ->whereNull('read_at')->where('sender_id','<>',$me->id)
            ->selectRaw('conversation_id, count(*) as c')->groupBy('conversation_id')->pluck('c','conversation_id');
            @endphp

            <ul>
                @forelse($myConvs as $c)
                @php
                $partnerSide = ($me->id===$c->buyer_id) ? $c->seller : $c->buyer;
                $un = $unreadsSide[$c->id] ?? 0;
                $active = $c->id === $conversation->id;
                $itemTitle = optional($c->item)->name ?? '商品';
                @endphp
                <li>
                    <a href="{{ route('conversations.show',$c) }}" class="item {{ $active?'active':'' }}">
                        <div class="item-head label">
                            <span class="item-title">{{ \Illuminate\Support\Str::limit($itemTitle, 28) }}</span>
                        </div>
                        @if($un>0) <span class="pill">{{ $un }}</span> @endif
                    </a>
                </li>
                @empty
                <li class="muted">取引中のスレッドはありません</li>
                @endforelse
            </ul>

    </aside>

    {{-- 右：チャット本体 --}}
    <section class="panel chat">

        {{-- （任意のデバッグ表示）
    <div class="muted" style="display:flex;gap:10px;padding:6px 12px;">
      <span>me={{ auth()->id() }}</span>
        <span>buyer={{ $conversation->buyer_id }}</span>
        <span>seller={{ $conversation->seller_id }}</span>
        <span>status={{ $conversation->status }}</span>
        <span>purchase_id={{ $conversation->purchase_id ?? 'null' }}</span>
</div> --}}

<h1 class="conv-heading">「{{ $partner->name }}」さんとの取引画面</h1>

{{-- ヘッダー：商品情報＋取引を完了するボタン --}}
<div class="panel-header">
    <div class="item-head">
        <div class="item-img">
            @if($imgUrl)
            <img src="{{ $imgUrl }}" alt="item">
            @else
            <span class="noimg">NO IMAGE</span>
            @endif
        </div>
        <div>
            <div class="item-name">{{ $itemName }}</div>
            <div class="muted">¥{{ $itemPrice }}</div>
        </div>
    </div>

    @php $canComplete = $isBuyer && $isOpen && !empty($conversation->purchase_id); @endphp
    <div>
        @if($canComplete)
        {{-- フォームにせず、星モーダルを開く --}}
        <button type="button" class="btn-complete" onclick="document.getElementById('modal-buyer').style.display='block'">
            取引を完了する
        </button>
        @else
        <button type="button" class="btn-disabled"
            title="{{ $isBuyer ? ($conversation->purchase_id ? 'ステータスがopen以外です' : '購入情報が見つかりません') : '購入者のみ完了できます' }}">
            取引を完了する
        </button>
        @endif
    </div>
</div>

{{-- メッセージ一覧 --}}
<div class="chat-body">
    @forelse($messages as $m)
    @php $mine = $m->sender_id === $me->id; @endphp

    <div class="bubble-row" style="justify-content:{{ $mine ? 'flex-end' : 'flex-start' }};">
        <div class="bubble {{ $mine ? 'mine' : '' }}">
            <div class="meta">
                {{ $m->sender->name }} ・ {{ $m->created_at->format('Y/m/d H:i') }}
                @if($m->read_at && !$mine) ・ 既読 @endif
            </div>

            <div class="bubble__body">{{ $m->body }}</div>

            @if($m->image_path)
            <div class="imgbox">
                <img src="{{ asset('storage/'.$m->image_path) }}" alt="image">
            </div>
            @endif
        </div>
    </div>

    {{-- 吹き出しの外・下にアクション --}}
    @if($mine)
    <div class="msg-actions {{ $mine ? 'right' : 'left' }}">
        <button type="button" class="action-link" data-edit-toggle="{{ $m->id }}">編集</button>

        <form action="{{ route('messages.destroy', [$conversation, $m]) }}" method="POST" class="inline">
            @csrf @method('DELETE')
            <button type="submit" class="action-link danger" onclick="return confirm('削除しますか？')">削除</button>
        </form>
    </div>

    {{-- インライン編集フォーム（初期非表示） --}}
    <form data-edit-form="{{ $m->id }}"
        action="{{ route('messages.update', [$conversation, $m]) }}"
        method="POST" enctype="multipart/form-data"
        class="edit-form" style="display:none;">
        @csrf @method('PATCH')
        <textarea name="body" rows="3" maxlength="400" style="width:100%;">{{ old('body', $m->body) }}</textarea>
        <div class="edit-form__row">
            <input type="file" name="image" accept=".jpeg,.jpg,.png">
            <button type="submit" class="btn-outline">更新</button>
            <button type="button" class="btn-ghost" data-edit-toggle="{{ $m->id }}">キャンセル</button>
        </div>
    </form>
    @endif

    @empty
    <p>まだメッセージはありません。</p>
    @endforelse

    {{-- ★ ここでページネーション＆ここで chat-body を閉じる --}}
    {{ $messages->links() }}
</div>

{{-- 入力フォーム（完了後は非表示） --}}
@if($conversation->status === 'open')
<form id="chatForm" class="chat-form" action="{{ route('messages.store',$conversation) }}" method="POST" enctype="multipart/form-data">
    @csrf

    @if ($errors->any())
    <div class="errors">
        <ul style="margin:0; padding-left:16px;">
            @foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
    @endif

    <div class="row">
        <textarea id="chatBody" name="body" placeholder="取引メッセージを記入してください">{{ old('body') }}</textarea>
        <input id="chatImage" type="file" name="image" accept=".jpeg,.jpg,.png" class="file-input-hidden">
        <label for="chatImage" class="btn-add-image" aria-label="画像を追加">画像を追加</label>
        <span id="fileName" class="file-name" aria-live="polite"></span>

        {{-- ▼ 紙飛行機そのものがボタン --}}
        <button type="submit" class="send-icon" aria-label="送信">
            <svg width="80" height="61" viewBox="0 0 80 61" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
                <!-- シンプルな斜め右上向きの紙飛行機（線画） -->
                <path d="M3 30 L75 3 L50 58 L38 38 L3 30 Z" stroke="black" stroke-width="2" fill="none" stroke-linejoin="round" />
                <path d="M75 3 L38 38" stroke="black" stroke-width="2" fill="none" />
            </svg>
        </button>
    </div>
</form>
@else
<div class="chat-form">
    <p class="muted">この取引は完了済みのため新規メッセージは送信できません。</p>
</div>
@endif

</section>
</div>

{{-- 購入者用：完了 + 評価（星UI） --}}
<div id="modal-buyer" class="modal modal--success" style="display:none;">
    <div class="modal__backdrop" data-close></div>
    <div class="modal__panel">
        <h3 class="modal__title">取引が完了しました</h3>
        <hr class="modal__hr" />
        <p class="modal__subtitle">今回の取引相手はどうでしたか？</p>

        <div class="stars" role="radiogroup" aria-label="評価を選択">
            @for($i=1; $i<=5; $i++)
                <button type="button" class="star-btn" data-val="{{ $i }}" aria-pressed="false" aria-label="{{ $i }}点">★</button>
                @endfor
        </div>

        <hr class="modal__hr" />

        <form method="POST" action="{{ route('purchases.complete_and_review', $conversation->purchase_id ?? 0) }}" class="modal__form">
            @csrf
            <input type="hidden" name="rating" id="ratingInput" required>
            <div class="modal__actions">
                <button type="submit" class="btn-send">送信する</button>
            </div>
        </form>
    </div>
</div>

{{-- 出品者用：評価のみ（購入者が完了済 & まだ自分が未評価ならオート表示） --}}
<div id="modal-seller" class="modal" style="display:none;">
    <div class="modal__backdrop" data-close></div>
    <div class="modal__panel">
        <h3 class="modal__title">購入者を評価する</h3>
        <form action="{{ route('reviews.store', $conversation->purchase_id ?? 0) }}" method="POST">
            @csrf
            <div class="form-row">
                <label>評価（1〜5）</label>
                <select name="rating" required>
                    @for($i=1;$i<=5;$i++) <option value="{{ $i }}">{{ $i }}</option> @endfor
                </select>
            </div>
            <div class="form-row">
                <label>コメント（任意）</label>
                <textarea name="comment" rows="3" placeholder="取引の感想など（任意）"></textarea>
            </div>
            <div class="modal__actions">
                <button type="button" class="btn-outline" data-close>あとで</button>
                <button type="submit" class="btn-primary">評価を送信</button>
            </div>
        </form>
    </div>
</div>

@php
// 出品者側：completed かつ 自分未評価なら自動表示
$hasPurchase = !empty($conversation->purchase_id);
$sellerShouldAutoOpen = (!$isBuyer)
&& ($conversation->status === 'completed')
&& $hasPurchase
&& !\App\Models\Review::where('purchase_id',$conversation->purchase_id)->where('reviewer_id',$me->id)->exists();
@endphp

<script>
    (() => {
        // ===== モーダル =====
        const buyerModal = document.getElementById('modal-buyer');
        const sellerModal = document.getElementById('modal-seller');
        const buyerOpenBtn = document.querySelector('.btn-complete');
        const closeEls = document.querySelectorAll('[data-close]');

        const openModal = (el) => el && (el.style.display = 'block');
        const closeModals = () => {
            if (buyerModal) buyerModal.style.display = 'none';
            if (sellerModal) sellerModal.style.display = 'none';
        };

        if (buyerOpenBtn && buyerModal) {
            buyerOpenBtn.addEventListener('click', (e) => {
                e.preventDefault();
                openModal(buyerModal);
            });
        }

        const sellerShouldAutoOpen = @json($sellerShouldAutoOpen);
        if (sellerShouldAutoOpen && sellerModal) openModal(sellerModal);

        closeEls.forEach(el => el.addEventListener('click', closeModals));
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeModals();
        });

        // ===== 星評価UI（購入者モーダル） =====
        if (buyerModal) {
            const stars = Array.from(buyerModal.querySelectorAll('.star-btn'));
            const ratingInput = buyerModal.querySelector('#ratingInput');

            // 既に選択済みなら初期表示を塗る
            const paint = (n) => {
                stars.forEach((btn, i) => {
                    const on = i < n;
                    btn.classList.toggle('is-filled', on);
                    btn.setAttribute('aria-pressed', on ? 'true' : 'false');
                });
            };
            if (ratingInput && ratingInput.value) paint(parseInt(ratingInput.value, 10));

            stars.forEach(btn => {
                btn.addEventListener('click', () => {
                    const val = parseInt(btn.dataset.val, 10) || 0;
                    ratingInput.value = val;
                    paint(val);
                });
                btn.addEventListener('keydown', (e) => {
                    const cur = parseInt(ratingInput.value || '0', 10) || 0;
                    if (e.key === 'ArrowRight' || e.key === 'ArrowUp') {
                        e.preventDefault();
                        const v = Math.min(5, cur + 1);
                        ratingInput.value = v;
                        paint(v);
                    }
                    if (e.key === 'ArrowLeft' || e.key === 'ArrowDown') {
                        e.preventDefault();
                        const v = Math.max(1, cur - 1);
                        ratingInput.value = v;
                        paint(v);
                    }
                });
            });

            const form = buyerModal.querySelector('form');
            if (form) form.addEventListener('submit', (e) => {
                if (!ratingInput.value) {
                    e.preventDefault();
                    alert('評価（星）を選択してください');
                }
            });
        }

        // ===== 画像ファイル名の表示 =====
        const fileInput = document.getElementById('chatImage');
        const fileName = document.getElementById('fileName');
        if (fileInput && fileName) {
            fileInput.addEventListener('change', () => {
                fileName.textContent = (fileInput.files && fileInput.files[0]) ? fileInput.files[0].name : '';
            });
        }

        // ===== チャット本文の下書き保持 =====
        const ta = document.getElementById('chatBody');
        const chatForm = document.getElementById('chatForm');
        if (ta && chatForm) {
            const key = 'chat_draft_{{ $conversation->id }}';
            const saved = localStorage.getItem(key);
            if (saved && !ta.value) ta.value = saved;
            ta.addEventListener('input', e => localStorage.setItem(key, e.target.value));
            chatForm.addEventListener('submit', () => localStorage.removeItem(key));
        }

        // ===== 編集フォームのトグル（イベント委譲で1本化） =====
        document.addEventListener('click', (e) => {
            const btn = e.target.closest('[data-edit-toggle]');
            if (!btn) return;
            const id = btn.getAttribute('data-edit-toggle');
            const form = document.querySelector(`[data-edit-form="${id}"]`);
            if (form) form.style.display = (form.style.display === 'none' || !form.style.display) ? 'block' : 'none';
        });
    })();
</script>

@endsection