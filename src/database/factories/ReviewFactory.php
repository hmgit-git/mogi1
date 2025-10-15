<?php

namespace Database\Factories;

use App\Models\{Review, Purchase, User};
use Illuminate\Database\Eloquent\Factories\Factory;

class ReviewFactory extends Factory
{
    protected $model = Review::class;

    public function definition()
    {
        $purchase = Purchase::factory()->create();
        $sellerId = $purchase->item->user_id;
        $buyerId  = $purchase->user_id;

        // reviewer(自分) → 相手を reviewee に
        $reviewerId = $this->faker->boolean ? $sellerId : $buyerId;
        $revieweeId = $reviewerId === $sellerId ? $buyerId : $sellerId;

        return [
            'purchase_id' => $purchase->id,
            'reviewer_id' => $reviewerId,
            'reviewee_id' => $revieweeId,
            'rating'      => $this->faker->numberBetween(3, 5),
            'comment'     => $this->faker->realText(40),
        ];
    }
}
