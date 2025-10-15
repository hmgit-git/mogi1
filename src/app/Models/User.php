<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'name',
        'email', 
        'password',
        'email_verified_at',
    ];



    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public function likes()
    {
        return $this->belongsToMany(Item::class, 'likes')->withTimestamps();
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    public function purchases()
    {
        return $this->hasMany(\App\Models\Purchase::class);
    }
    public function items()
    {
        return $this->hasMany(Item::class);
    }
    public function likedItems()
    {
        return $this->belongsToMany(Item::class, 'likes')->withTimestamps();
    }

    // --- 受け取ったレビュー（平均評価用） ---
    public function reviewsReceived()
    {
        return $this->hasMany(Review::class, 'reviewee_id');
    }

    // --- 取引（buyer/seller）関係 ---
    public function conversationsAsBuyer()
    {
        return $this->hasMany(Conversation::class, 'buyer_id');
    }

    public function conversationsAsSeller()
    {
        return $this->hasMany(Conversation::class, 'seller_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedReviews()
    {
        return $this->hasMany(\App\Models\Review::class, 'reviewee_id');
    }

    public function getRatingCountAttribute(): int
    {
        return $this->receivedReviews()->count();
    }

    public function getRatingAverageRoundedAttribute(): ?int
    {
        $avg = $this->receivedReviews()->avg('rating'); // ratingは1〜5
        return is_null($avg) ? null : (int) round($avg); // 四捨五入
    }
}
