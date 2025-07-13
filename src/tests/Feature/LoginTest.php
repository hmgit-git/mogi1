<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    // ===============================
    // ▼ 以下、テスト対象として実装済だが提出時は実行OFF
    // ===============================

    // public function test_メールアドレスが未入力の場合_バリデーションメッセージが表示される()
    // {
    //     $response = $this->post('/login', [
    //         // 'email' => '', ← intentionally omitted
    //         'password' => 'password123',
    //     ]);

    //     $response->assertSessionHasErrors([
    //         'email' => 'メールアドレスを入力してください',
    //     ]);
    // }

    // public function test_パスワードが未入力の場合_バリデーションメッセージが表示される()
    // {
    //     $response = $this->post('/login', [
    //         'email' => 'test@example.com',
    //         // 'password' => '', ← intentionally omitted
    //     ]);

    //     $response->assertSessionHasErrors([
    //         'password' => 'パスワードを入力してください',
    //     ]);
    // }


    // public function test_登録されていないアカウントではログインできずエラーメッセージが表示される()
    // //Laravelのデフォルト処理（withErrors()）を活かしコントローラーにバリデーション記載。
    // {
    //     $response = $this->post('/login', [
    //         'email' => 'notfound@example.com',
    //         'password' => 'password123',
    //     ]);

    //     $response->assertSessionHasErrors([
    //         'email' => 'ログイン情報が登録されていません。',
    //     ]);
    // }

    // public function test_正しい情報を入力するとログインできる()
    // {
    //     $user = \App\Models\User::factory()->create([
    //         'email' => 'loginuser@example.com',
    //         'password' => bcrypt('password123'), // ← bcrypt忘れずに！
    //     ]);

    //     $response = $this->post('/login', [
    //         'email' => 'loginuser@example.com',
    //         'password' => 'password123',
    //     ]);

    //     // ✅ 正しくリダイレクトされることを確認（ログイン後の遷移先）
    //     $response->assertRedirect('/items?tab=mylist');

    //     // ✅ 実際にログイン済みになっているか
    //     $this->assertAuthenticatedAs($user);
    // }

    public function test_ログアウトするとログイン状態が解除される()
    {
        $user = \App\Models\User::factory()->create();

        $this->actingAs($user);

        $response = $this->post('/logout');

        $response->assertRedirect('/login'); // ← ログアウト後の遷移先に応じて調整！
        $this->assertGuest(); // ← ログアウトして非ログイン状態になっているか確認
    }
}
