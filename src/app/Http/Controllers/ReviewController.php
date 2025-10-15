<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReviewRequest;
use App\Mail\TransactionCompletedMail;
use App\Models\Conversation;
use App\Models\Purchase;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;

class ReviewController extends Controller
{
    /**
     * 出品者 or 購入者が「評価のみ」を送る（出品者側のモーダルなど）
     * POST /purchases/{purchase}/reviews
     */
    public function store(StoreReviewRequest $req, Purchase $purchase): RedirectResponse
    {
        $me = $req->user();

        // この購入の当事者のみ
        $sellerId = $purchase->item->user_id; // 出品者
        $buyerId  = $purchase->user_id;       // 購入者
        abort_unless(in_array($me->id, [$sellerId, $buyerId], true), 403);

        // 自分が評価する相手
        $revieweeId = ($me->id === $sellerId) ? $buyerId : $sellerId;

        // 二重作成防止（purchase_id + reviewer_id で一意）
        Review::firstOrCreate(
            ['purchase_id' => $purchase->id, 'reviewer_id' => $me->id],
            [
                'reviewee_id' => $revieweeId,
                'rating'      => (int) $req->rating,
                'comment'     => (string) ($req->comment ?? ''),
            ]
        );

        return redirect()->route('items.index')->with('success', '評価を送信しました');
    }

    /**
     * 購入者が「取引完了 + 評価」を一括送信
     * POST /purchases/{purchase}/complete-and-review
     */
    public function completeAndReview(StoreReviewRequest $req, Purchase $purchase): RedirectResponse
    {
        $me = $req->user();

        // 1) 権限：購入者のみ完了可
        abort_unless($me->id === $purchase->user_id, 403);

        // 2) 会話を completed に（purchase に紐づく会話がある前提）
        Conversation::where('purchase_id', $purchase->id)->update(['status' => 'completed']);

        // 3) 出品者へメール（MailHog/Mailtrap）
        //    リレーションが無い/未ロードでも落ちないように安全に辿る
        $seller = optional($purchase->item)->user; // App\Models\User|null
        if ($seller && $seller->email) {
            Mail::to($seller->email)->send(new TransactionCompletedMail($purchase));
        }

        // 4) 評価を保存（レビュー対象＝相手）
        $sellerId   = $purchase->item->user_id;
        $buyerId    = $purchase->user_id;
        $revieweeId = ($me->id === $sellerId) ? $buyerId : $sellerId;

        Review::firstOrCreate(
            ['purchase_id' => $purchase->id, 'reviewer_id' => $me->id],
            [
                'reviewee_id' => $revieweeId,
                'rating'      => (int) $req->rating,
                'comment'     => (string) ($req->comment ?? ''),
            ]
        );

        // 送信後は商品一覧へ（US004-FN014）
        return redirect()->route('items.index')->with('success', '取引を完了し、評価を送信しました');
    }
}
