<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();
        $tab = $request->query('tab');
        $keyword = $request->query('keyword');

        $items = Item::query()
            ->when($tab === 'mylist' && $userId, function ($query) use ($userId) {
                return $query->whereHas('likedUsers', function ($q) use ($userId) {
                    $q->where('user_id', $userId);
                });
            })
            ->when($tab !== 'mylist' && $userId, function ($query) use ($userId) {
                return $query->where('user_id', '!=', $userId);
            })
            ->when($keyword, function ($query) use ($keyword) {
                return $query->where('name', 'like', '%' . $keyword . '%');
            })
            ->get();

        return view('items.index', [
            'tab' => $tab,
            'items' => $items,
            'keyword' => $keyword,
        ]);
    }

    public function show($id)
    {
        $item = Item::with(['likedUsers', 'comments.user', 'categories'])->findOrFail($id);

        return view('items.show', compact('item'));
    }


    public function toggleLike($id)
    {
        $user = Auth::user();
        $item = Item::findOrFail($id);

        if ($user->likes->contains($item->id)) {
            $user->likes()->detach($item->id);
            $liked = false;
        } else {
            $user->likes()->attach($item->id);
            $liked = true;
        }

        if (request()->ajax()) {
            return response()->json([
                'liked' => $liked,
                'likes_count' => $item->likedUsers()->count(),
            ]);
        }

        return back();
    }
}
