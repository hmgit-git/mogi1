<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class UserProfileEditFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_ユーザー情報変更画面に初期値が表示される()
    {
        // 1. ユーザー作成（画像・名前・郵便番号・住所など設定）
        Storage::fake('public');
        $user = User::factory()->create([
            'username' => 'テストユーザー',
            'profile_image' => 'images/profiles/test.jpg',
            'zip' => '123-4567',
            'address' => '東京都渋谷区道玄坂1-2-3',
            'building' => 'テストビル202',
        ]);

        // 画像ファイルを仮置き
        Storage::disk('public')->put('images/profiles/test.jpg', UploadedFile::fake()->image('test.jpg'));

        // 2. プロフィール編集ページにアクセス
        $response = $this->actingAs($user)->get('/profile/edit');

        // 3. 各項目の初期値が表示されていることを確認
        $response->assertStatus(200);
        $response->assertSee('テストユーザー');
        $response->assertSee('123-4567');
        $response->assertSee('東京都渋谷区道玄坂1-2-3');
        $response->assertSee('テストビル202');
        $response->assertSee('images/profiles/test.jpg'); // 画像パスがビューにあるか
    }
}
