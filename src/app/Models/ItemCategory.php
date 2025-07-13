<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemCategory extends Model
{
    use HasFactory;

    protected $table = 'item_category'; // テーブル名は複数形ではない！

    protected $fillable = [
        'item_id',
        'category_id',
    ];

    public $timestamps = false;
}
