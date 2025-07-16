<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\URL;
use App\Models\User;

class RegisterTest extends TestCase
{
    use RefreshDatabase;


    public function test_名前が未入力の場合_バリデーションメッセージが表示される()
    {
        $response = $this->post('/register', [
            // 'username' => '', ← intentionally omitted
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);


        $response->assertSessionHasErrors([
            'username' => 'お名前を入力してください',
        ]);
    }

    public function test_メールアドレスが未入力の場合_バリデーションメッセージが表示される()
    {
        $response = $this->post('/register', [
            'username' => 'テストユーザー',
            // 'email' => '', ← intentionally omitted
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors([
            'email' => 'メールアドレスを入力してください',
        ]);
    }
    public function test_パスワードが未入力の場合_バリデーションメッセージが表示される()
    {
        $response = $this->post('/register', [
            'username' => 'テストユーザー',
            'email' => 'test@example.com',
            // パスワード intentionally omitted
        ]);

        $response->assertSessionHasErrors([
            'password' => 'パスワードを入力してください',
        ]);
    }

    public function test_パスワードが7文字以下の場合_バリデーションメッセージが表示される()
    {
        $response = $this->post('/register', [
            'username' => 'テストユーザー',
            'email' => 'shortpass@example.com',
            'password' => '1234567', // ← わざと7文字
            'password_confirmation' => '1234567',
        ]);

        $response->assertSessionHasErrors([
            'password' => 'パスワードは8文字以上で入力してください',
        ]);
    }

    public function test_パスワード確認が一致しない場合_バリデーションメッセージが表示される()
    {
        $response = $this->post('/register', [
            'username' => 'テストユーザー',
            'email' => 'mismatch@example.com',
            'password' => 'password123',
            'password_confirmation' => 'differentpass',
        ]);

        $response->assertSessionHasErrors([
            'password_confirmation' => 'パスワードと一致しません',
        ]);
    }

    public function test_全ての項目が正しく入力された場合_登録成功してメール認証画面にリダイレクトされる()
    {
        $response = $this->post('/register', [
            'username' => '登録成功太郎',
            'email' => 'verifyme@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // 登録後はログイン画面じゃなくメール認証画面にリダイレクトされる
        $response->assertRedirect('/email/verify');

        // ユーザー情報がデータベースに存在することを確認
        $this->assertDatabaseHas('users', [
            'email' => 'verifyme@example.com',
        ]);
    }

    public function test_メール認証後にプロフィール設定画面に遷移する()
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        // 認証状態にする（ログイン）
        $this->actingAs($user);

        // 認証URLにアクセス
        $response = $this->get($url);

        $response->assertRedirect('/mypage/profile');

        // 認証済みになったか確認
        $this->assertNotNull($user->fresh()->email_verified_at);
    }
}
