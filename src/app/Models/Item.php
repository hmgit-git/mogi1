<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'brand',
        'price',
        'image_path',
        'status',
        'is_sold',
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'item_category');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
