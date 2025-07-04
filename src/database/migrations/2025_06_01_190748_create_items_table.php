<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsTable extends Migration
{
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description');
            $table->string('brand')->nullable();
            $table->integer('price');
            $table->string('image_path')->nullable();
            $table->string('condition');
            $table->boolean('is_sold')->default(false);
            $table->timestamps();
        });
    }
}
