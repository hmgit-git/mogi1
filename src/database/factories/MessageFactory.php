<?php

namespace Database\Factories;

use App\Models\{Message, Conversation};
use Illuminate\Database\Eloquent\Factories\Factory;

class MessageFactory extends Factory
{
    protected $model = Message::class;

    public function definition()
    {
        // 会話が先にある前提。なければ生成。
        $conv = Conversation::factory()->create();

        // 送信者は buyer/seller のどちらか
        $senderId = $this->faker->boolean ? $conv->buyer_id : $conv->seller_id;

        return [
            'conversation_id' => $conv->id,
            'sender_id'       => $senderId,
            'body'            => $this->faker->realText(30),
            'image_path'      => null,
        ];
    }

    public function forConversation(Conversation $conv)
    {
        return $this->state(function () use ($conv) {
            $senderId = $this->faker->boolean ? $conv->buyer_id : $conv->seller_id;
            return [
                'conversation_id' => $conv->id,
                'sender_id'       => $senderId,
            ];
        });
    }
}
