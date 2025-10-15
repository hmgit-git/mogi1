<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str; 

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
        'condition',
        'is_sold',
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'item_category');
    }
    public function likedUsers()
    {
        return $this->belongsToMany(User::class, 'likes')->withTimestamps();
    }
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function getImageUrlAttribute(): ?string
    {
        $p = $this->image_path;
        if (!$p) return null;

        if (Str::startsWith($p, ['http://', 'https://'])) {
            return $p;
        }
        if (Str::startsWith($p, ['/storage/', 'storage/'])) {
            return asset(ltrim($p, '/'));
        }
        return asset('storage/' . $p);
    }
}
