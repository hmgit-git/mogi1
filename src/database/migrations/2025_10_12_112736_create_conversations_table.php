<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConversationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('conversations', function (Blueprint $t) {
            $t->id();
            $t->foreignId('item_id')->constrained()->cascadeOnDelete();
            $t->foreignId('buyer_id')->constrained('users')->cascadeOnDelete();
            $t->foreignId('seller_id')->constrained('users')->cascadeOnDelete();
            $t->foreignId('purchase_id')->nullable()->constrained('purchases')->nullOnDelete();
            $t->enum('status', ['open', 'completed', 'cancelled'])->default('open');
            $t->timestamp('last_message_at')->nullable()->index();
            $t->timestamps();
            $t->unique(['item_id', 'buyer_id', 'seller_id', 'purchase_id']);
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('conversations');
    }
}
