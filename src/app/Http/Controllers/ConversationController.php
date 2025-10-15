<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Item;
use App\Models\Message;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ConversationController extends Controller
{
    // 取引中一覧（新着順＋未読数）
    public function index()
    {
        $list = Conversation::forUser(auth()->id())
            ->where('status', 'open')
            ->with([
                'item:id,name,price,image_path',
                'buyer:id,name',
                'seller:id,name',
                'messages' => fn($q) => $q->latest()->limit(1),
            ])
            ->orderByDesc('last_message_at')
            ->paginate(24);

        $unreads = Message::whereIn('conversation_id', $list->pluck('id'))
            ->whereNull('read_at')
            ->where('sender_id', '<>', auth()->id())
            ->selectRaw('conversation_id, COUNT(*) AS c')
            ->groupBy('conversation_id')
            ->pluck('c', 'conversation_id');

        return view('conversations.index', compact('list', 'unreads'));
    }


    // 取引チャット画面
    public function show(Conversation $conversation)
    {
        $this->authorize('view', $conversation);

        $messages = $conversation->messages()
            ->with('sender')
            ->orderBy('id')
            ->paginate(50);

        // 自分以外からの未読を既読に
        $conversation->messages()
            ->whereNull('read_at')
            ->where('sender_id', '<>', auth()->id())
            ->update(['read_at' => now()]);

        return view('conversations.show', compact('conversation', 'messages'));
    }

    // 商品詳細から「質問する」で開始（購入前/後どちらでも使える）
    public function startFromItem(Item $item)
    {
        $buyerId = auth()->id();
        $sellerId = $item->user_id;
        abort_if($buyerId === $sellerId, 403);

        $conv = Conversation::firstOrCreate(
            ['item_id' => $item->id, 'buyer_id' => $buyerId, 'seller_id' => $sellerId, 'purchase_id' => null],
            ['last_message_at' => now()]
        );

        return redirect()->route('conversations.show', $conv);
    }

    // 購入者が取引完了 → 出品者へメール
    public function complete(Purchase $purchase)
    {
        $me = auth()->id();
        abort_unless($me === $purchase->user_id, 403);

        Conversation::where('purchase_id', $purchase->id)->update(['status' => 'completed']);

        Mail::to($purchase->item->user->email)->send(new \App\Mail\TransactionCompletedMail($purchase));

        return back()->with('success', '取引を完了しました（出品者へメール送信）');
    }
}
