<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\StripeController;



/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/


// ログイン処理
Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login');

//ログイン後、商品一覧ページ
Route::get('/items', [ItemController::class, 'index'])->name('items.index');


// 商品一覧（トップ）ページ
Route::get('/', [ItemController::class, 'index'])->name('items.index');
// マイページ
Route::get('/mypage', [ProfileController::class, 'mypage'])
    ->middleware(['auth'])
    ->name('mypage');

// 会員登録フォーム表示
Route::get('/register', [RegisterController::class, 'show'])->name('register');

// 会員登録処理
Route::post('/register', [RegisterController::class, 'store']);

// メール認証画面
Route::get('/email/verify', function () {
    return view('auth.verify-email'); // このBladeを用意してね
})->middleware('auth')->name('verification.notice');

// メール認証リンク処理
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/mypage/profile');
})->middleware(['auth', 'signed'])->name('verification.verify');


// メール認証リンク再送
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', '認証リンクを再送しました！');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');


// プロフィール設定画面（住所など設定）を表示
Route::get('/mypage/profile', [ProfileController::class, 'editSetting'])
    ->middleware(['auth', 'verified'])
    ->name('mypage.profile');


// ログアウト後ログインページへリダイレクト（デバッグ用）
Route::get('/logout-and-login', function () {
    Auth::logout();
    return redirect('/login');
})->name('logout-and-go-login');

// 商品詳細ページ
Route::get('/item/{id}', [ItemController::class, 'show'])->name('items.show');

// いいね
Route::post('/item/{item}/like', [LikeController::class, 'toggle'])
    ->middleware('auth')
    ->name('items.like');

// プロフィール編集
Route::get('/profile/edit', [ProfileController::class, 'edit'])
    ->middleware(['auth', 'verified'])
    ->name('profile.edit');

Route::post('/profile/edit', [ProfileController::class, 'update'])
    ->middleware(['auth', 'verified'])
    ->name('profile.update');


// 購入確認画面の表示（GET）
Route::get('/purchase/{item}', [PurchaseController::class, 'show'])
    ->middleware(['auth'])
    ->name('purchase.show');

// 購入処理の実行（POST）
Route::post('/purchase/{item}', [PurchaseController::class, 'store'])
    ->middleware(['auth'])
    ->name('purchase.store');

// Sripe実行
Route::get('/stripe/checkout/{item_id}', [StripeController::class, 'checkout'])->name('stripe.checkout');
Route::get('/stripe/success/{item_id}', [StripeController::class, 'success'])->name('stripe.success');
Route::get('/stripe/cancel', [StripeController::class, 'cancel'])->name('stripe.cancel');

// 送付先変更
Route::get('/purchase/{item}/address', [PurchaseController::class, 'editAddress'])->name('purchase.address.edit');
Route::post('/purchase/{item}/address', [PurchaseController::class, 'updateAddress'])->name('purchase.address.update');

// コメント送信機能
Route::post('/item/{item}/comment', [CommentController::class, 'store'])
    ->middleware(['auth'])
    ->name('comments.store');

// 出品画面
Route::get('/sell', [ItemController::class, 'create'])
    ->middleware(['auth'])
    ->name('items.create');

// 商品出品の登録処理（POST送信）
Route::post('/sell', [ItemController::class, 'store'])
    ->middleware(['auth'])
    ->name('items.store');
