<?php

namespace App\Policies;

use App\Models\Message;
use App\Models\User;

class MessagePolicy
{
    // 自分が送ったメッセージだけ編集OK
    public function update(User $user, Message $message): bool
    {
        return $message->sender_id === $user->id
            && ($message->conversation->buyer_id === $user->id
                || $message->conversation->seller_id === $user->id);
    }

    // 自分が送ったメッセージだけ削除OK
    public function delete(User $user, Message $message): bool
    {
        return $message->sender_id === $user->id
            && ($message->conversation->buyer_id === $user->id
                || $message->conversation->seller_id === $user->id);
    }
}
