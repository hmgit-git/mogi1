<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'item_id',
        'shipping_zip',
        'shipping_address',
        'shipping_building',
    ];

    public function item()
    {
        return $this->belongsTo(\App\Models\Item::class);
    }
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

}
