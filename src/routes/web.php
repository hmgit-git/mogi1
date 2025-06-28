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

// 商品出品ページ
Route::get('/items/create', function () {
    return view('items.create');
})->middleware(['auth'])->name('items.create');

// マイページ
Route::get('/mypage', function () {
    return view('mypage');
})->middleware(['auth', 'verified'])->name('mypage');

// プロフィール編集
Route::get('/profile/edit', function () {
    return view('profile.edit');
})->middleware(['auth', 'verified'])->name('profile.edit');

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
    return redirect()->route('profile.edit');
})->middleware(['auth', 'signed'])->name('verification.verify');

// メール認証リンク再送
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', '認証リンクを再送しました！');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

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

// 購入画面
Route::get('/purchase/{item}', function ($itemId) {
    return view('items.purchase', ['itemId' => $itemId]);
})->middleware(['auth'])->name('purchase');

// コメント送信機能
Route::post('/item/{item}/comment', [CommentController::class, 'store'])
    ->middleware(['auth']) // ログインユーザーのみ
    ->name('comments.store');
