<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Category;
use App\Http\Requests\ItemStoreRequest;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();
        $tab = $request->query('tab');
        $keyword = $request->query('keyword');

        $items = Item::query()
            ->when($tab === 'mylist' && $userId, function ($query) use ($userId, $keyword) {
                return $query->whereHas('likedUsers', function ($q) use ($userId) {
                    $q->where('user_id', $userId);
                })
                    ->when($keyword, function ($q2) use ($keyword) {
                        return $q2->where('name', 'like', '%' . $keyword . '%');
                    });
            })
            ->when($tab !== 'mylist' && $userId, function ($query) use ($userId, $keyword) {
                return $query->where('user_id', '!=', $userId)
                    ->when($keyword, function ($q2) use ($keyword) {
                        return $q2->where('name', 'like', '%' . $keyword . '%');
                    });
            })
            ->when(!$userId, function ($query) use ($keyword) {
                return $query->when($keyword, function ($q) use ($keyword) {
                    return $q->where('name', 'like', '%' . $keyword . '%');
                });
            })
            ->when(strlen($keyword), function ($query) use ($keyword) {
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
    public function create()
    {
        $categories = Category::all();
        $conditions = ['新品', '未使用に近い', '目立った傷や汚れなし', 'やや傷や汚れあり', '全体的に状態が悪い'];

        return view('items.create', compact('categories', 'conditions'));
    }

    public function store(ItemStoreRequest $request)
    {
        // 画像アップロード（任意）
        $imagePath = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('public/items');
            $imagePath = str_replace('public/', 'storage/', $path);
        }

        // 商品登録
        $item = Item::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'brand' => $request->brand,
            'description' => $request->description,
            'condition' => $request->condition,
            'price' => $request->price,
            'image_path' => $imagePath,
        ]);

        // 中間テーブルへカテゴリー登録
        $item->categories()->attach($request->categories);

        return redirect()->route('items.index');
    }
}
