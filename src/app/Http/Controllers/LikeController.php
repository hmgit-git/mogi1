<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    public function toggle(Item $item)
    {

        $user = Auth::user();

        if ($item->likedUsers()->where('user_id', $user->id)->exists()) {
            $item->likedUsers()->detach($user->id);
            $liked = false;
        } else {
            $item->likedUsers()->attach($user->id);
            $liked = true;
        }

        return response()->json([
            'liked' => $liked,
            'likes_count' => $item->likedUsers()->count(),
        ]);
    }
}
