<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/mypage', function () {
    return view('mypage');
})->middleware(['auth']);
Route::get('/item/create', function () {
    return view('item.create');
})->middleware(['auth']);
Route::get('/profile/edit', function () {
    return view('profile.edit');
})->middleware(['auth', 'verified']);
Route::get('/register', [RegisterController::class, 'show'])->name('register');
Route::post('/register', [RegisterController::class, 'store']);

// メール認証の通知再送
Route::get('/email/verify', function () {
    return view('auth.verify-email'); // Blade作る必要あり
})->middleware('auth')->name('verification.notice');

// 認証リンクからのアクセス（メールのリンクをクリックしたときに実行）
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill(); // メール認証を完了
    return redirect('/profile/edit'); // 認証後に遷移したいページへ
})->middleware(['auth', 'signed'])->name('verification.verify');

// 再送信処理
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', '認証リンクを再送しました！');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// 例：mypage には email verified ユーザーだけアクセス可
Route::get('/mypage', function () {
    return view('mypage');
})->middleware(['auth', 'verified']);

//ログイン後商品一覧画面
Route::get('/items', function () {
    return view('items.index');
})->middleware('auth');
Route::get('/logout-and-login', function () {
    Auth::logout();
    return redirect('/login');
})->name('logout-and-go-login');
