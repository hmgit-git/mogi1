<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ReviewController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// -------------------- Public（誰でも） --------------------

// トップページ（商品一覧と同じ中身にしたい場合は index を呼ぶ）
Route::get('/', [ItemController::class, 'index'])->name('home');

// 商品一覧（公式な一覧の名前はこっちに寄せる）
Route::get('/items', [ItemController::class, 'index'])->name('items.index');

// 商品詳細
Route::get('/item/{id}', [ItemController::class, 'show'])->name('items.show');

// 会員登録フォーム＆登録
Route::get('/register', [RegisterController::class, 'show'])->name('register');
Route::post('/register', [RegisterController::class, 'store']);

// ログイン（POST）
Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login');

// Stripe（要件次第で auth を付けてもOK）
Route::get('/stripe/checkout/{item_id}', [StripeController::class, 'checkout'])->name('stripe.checkout');
Route::get('/stripe/success/{item_id}',  [StripeController::class, 'success'])->name('stripe.success');
Route::get('/stripe/cancel',              [StripeController::class, 'cancel'])->name('stripe.cancel');

// デバッグ：ログアウトしてログイン画面へ
Route::get('/logout-and-login', function () {
    Auth::logout();
    return redirect('/login');
})->name('logout-and-go-login');


// -------------------- Auth（ログイン必須） --------------------
Route::middleware('auth')->group(function () {

    // マイページ
    Route::get('/mypage', [ProfileController::class, 'mypage'])->name('mypage');

    // いいね
    Route::post('/item/{item}/like', [LikeController::class, 'toggle'])->name('items.like');

    // コメント投稿
    Route::post('/item/{item}/comment', [CommentController::class, 'store'])->name('comments.store');

    // 出品
    Route::get('/sell',  [ItemController::class, 'create'])->name('items.create');
    Route::post('/sell', [ItemController::class, 'store'])->name('items.store');

    // 購入（表示/実行）
    Route::get('/purchase/{item}',  [PurchaseController::class, 'show'])->name('purchase.show');
    Route::post('/purchase/{item}', [PurchaseController::class, 'store'])->name('purchase.store');

    // 送付先変更
    Route::get('/purchase/{item}/address',  [PurchaseController::class, 'editAddress'])->name('purchase.address.edit');
    Route::post('/purchase/{item}/address', [PurchaseController::class, 'updateAddress'])->name('purchase.address.update');

    // --- Email Verification ---
    Route::get('/email/verify', function () {
        return view('auth.verify-email'); // Blade 用意済み
    })->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect('/mypage/profile');
    })->middleware('signed')->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('message', '認証リンクを再送しました！');
    })->middleware('throttle:6,1')->name('verification.send');

    // --- Verified（メール認証済み 限定エリア） ---
    Route::middleware('verified')->group(function () {
        // プロフィール編集
        Route::get('/mypage/profile', [ProfileController::class, 'editSetting'])->name('mypage.profile');
        Route::get('/profile/edit',   [ProfileController::class, 'edit'])->name('profile.edit');
        Route::post('/profile/edit',  [ProfileController::class, 'update'])->name('profile.update');
    });

    // --- 取引チャット（US001〜） ---
    Route::get('/conversations', [ConversationController::class, 'index'])->name('conversations.index');
    Route::get('/conversations/{conversation}', [ConversationController::class, 'show'])->name('conversations.show');
    Route::post('/items/{item}/ask', [ConversationController::class, 'startFromItem'])->name('conversations.start');

    Route::post('/conversations/{conversation}/messages', [MessageController::class, 'store'])->name('messages.store');
    Route::patch('/conversations/{conversation}/messages/{message}', [MessageController::class, 'update'])->name('messages.update');
    Route::delete('/conversations/{conversation}/messages/{message}', [MessageController::class, 'destroy'])->name('messages.destroy');

    // 取引完了（購入者）＆レビュー（US004/US005）
    Route::post('/purchases/{purchase}/complete', [ConversationController::class, 'complete'])->name('purchases.complete');
    Route::post('/purchases/{purchase}/reviews',  [ReviewController::class, 'store'])->name('reviews.store');
    
    // 完了+評価を一括で（購入者側のモーダル送信先）
    Route::post('/purchases/{purchase}/complete-and-review', [\App\Http\Controllers\ReviewController::class, 'completeAndReview'])
        ->name('purchases.complete_and_review');
});
