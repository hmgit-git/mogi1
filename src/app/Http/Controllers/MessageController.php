<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMessageRequest;
use App\Http\Requests\UpdateMessageRequest;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Support\Facades\Storage;
use App\Notifications\MessageReceived;

class MessageController extends Controller
{
    public function store(StoreMessageRequest $req, Conversation $conversation)
    {
        // 会話の当事者のみ送信可
        $this->authorize('send', $conversation);

        // 画像アップロード
        $path = $req->hasFile('image')
            ? $req->file('image')->store('chat_images', 'public')
            : null;

        // メッセージ生成
        $message = $conversation->messages()->create([
            'sender_id'  => auth()->id(),
            'body'       => $req->body,
            'image_path' => $path,
        ]);

        // 並び替え指標
        $conversation->update(['last_message_at' => now()]);

        // 受信者（自分→相手）
        $recipient = auth()->id() === $conversation->buyer_id
            ? $conversation->seller
            : $conversation->buyer;

        // 相手が取得できていて、自分自身ではない場合のみ通知
        if ($recipient && $recipient->id !== auth()->id()) {
            $recipient->notify(new MessageReceived($message));
        }

        return back()->with('success', 'メッセージを送信しました');
    }

    public function update(UpdateMessageRequest $req, Conversation $conversation, Message $message)
    {
        $this->authorize('modifyMessage', $message);

        $data = ['body' => $req->body];

        if ($req->hasFile('image')) {
            if ($message->image_path) {
                Storage::disk('public')->delete($message->image_path);
            }
            $data['image_path'] = $req->file('image')->store('chat_images', 'public');
        }

        $message->update($data);

        return back()->with('success', 'メッセージを編集しました');
    }

    public function destroy(Conversation $conversation, Message $message)
    {
        $this->authorize('modifyMessage', $message);

        if ($message->image_path) {
            Storage::disk('public')->delete($message->image_path);
        }
        $message->delete();

        return back()->with('success', 'メッセージを削除しました');
    }
}
