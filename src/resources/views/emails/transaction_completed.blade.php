<p>{{ optional(optional($purchase->item)->user)->name ?? '出品者' }} 様</p>
<p>以下の取引が購入者により完了されました。</p>
<ul>
    <li>商品：{{ optional($purchase->item)->name ?? '商品' }}</li>
    <li>購入者：{{ optional($purchase->user)->name ?? '購入者' }}</li>
    <li>取引ID：{{ $purchase->id }}</li>
</ul>
<p>アプリにログインして、購入者の評価をお願いします。</p>