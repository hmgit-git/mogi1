<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemCategory extends Model
{
    use HasFactory;

    protected $table = 'item_category'; // テーブル名が複数形じゃないので明示！

    protected $fillable = [
        'item_id',
        'category_id',
    ];

    public $timestamps = false; // タイムスタンプは不要ならfalseに
}
