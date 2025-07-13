<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class LikeController extends Controller
{

    public function toggle(Item $item)
    {
        $user = Auth::user();
        Log::debug('toggle like start', ['user_id' => $user->id, 'item_id' => $item->id]);

        if ($item->likedUsers()->where('user_id', $user->id)->exists()) {
            $item->likedUsers()->detach($user->id);
            Log::debug('いいね解除');
            $liked = false;
        } else {
            $item->likedUsers()->attach($user->id);
            Log::debug('いいね追加');
            $liked = true;
        }

        return response()->json([
            'liked' => $liked,
            'likes_count' => $item->likedUsers()->count(),
        ]);
    }
}
