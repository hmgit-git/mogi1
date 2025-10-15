<?php

namespace App\Mail;

use App\Models\Purchase;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TransactionCompletedMail extends Mailable
{
    use Queueable, SerializesModels;

    /** @var \App\Models\Purchase */
    public $purchase;

    /**
     * Create a new message instance.
     */
    public function __construct(Purchase $purchase)
    {
        $this->purchase = $purchase;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->from(config('mail.from.address'), config('mail.from.name'))
            ->subject('取引が完了しました')
            ->view('emails.transaction_completed', [
                'purchase' => $this->purchase,
                'item'     => $this->purchase->item,                 // 商品
                'buyer'    => $this->purchase->user,                 // 購入者
                'seller'   => optional($this->purchase->item)->user, // 出品者（null安全）
            ]);
    }
}
