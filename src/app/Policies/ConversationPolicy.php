<?php

namespace App\Policies;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;

class ConversationPolicy
{
    public function view(User $user, Conversation $conv): bool
    {
        return in_array($user->id, [$conv->buyer_id, $conv->seller_id], true);
    }

    public function send(User $user, Conversation $conv): bool
    {
        return $this->view($user, $conv);
    }

    public function modifyMessage(User $user, Message $message): bool
    {
        return $user->id === $message->sender_id;
    }
}
