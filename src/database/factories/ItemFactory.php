<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    protected $model = Item::class;

    public function definition(): array
    {
        $conditions = ['新品', '未使用に近い', '目立った傷や汚れなし', 'やや傷や汚れあり', '全体的に状態が悪い'];

        return [
            'user_id' => User::factory(), // ユーザーも同時に作成
            'name' => $this->faker->word(),
            'brand' => $this->faker->company(),
            'description' => $this->faker->realText(100),
            'condition' => $this->faker->randomElement($conditions),
            'price' => $this->faker->numberBetween(100, 10000),
            'image_path' => 'storage/items/dummy.jpg', // 仮の画像パス
            'is_sold' => false,
        ];
    }
}
