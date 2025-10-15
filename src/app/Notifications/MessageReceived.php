<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Message;

class MessageReceived extends Notification
{
    use Queueable;

    public Message $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    public function via($notifiable)
    {
        return ['mail']; // 必要に応じて 'database' 等を追加
    }

    public function toMail($notifiable)
    {
        $conv = $this->message->conversation;
        $url  = route('conversations.show', $conv);

        return (new MailMessage)
            ->subject('新しい取引メッセージが届きました')
            ->greeting('こんにちは！')
            ->line('取引チャットに新しいメッセージがあります。')
            ->line('商品名：' . optional($conv->item)->name)
            ->line('送信者：' . optional($this->message->sender)->name)
            ->action('取引チャットを開く', $url)
            ->line('引き続き、当アプリをよろしくお願いいたします。');
    }
}
