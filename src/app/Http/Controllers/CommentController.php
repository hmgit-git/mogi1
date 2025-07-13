<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Comment;
use App\Http\Requests\CommentRequest;

class CommentController extends Controller
{
    public function store(CommentRequest $request, Item $item)
    {
        $item->comments()->create([
            'user_id' => auth()->id(),
            'content' => $request->input('content'),
        ]);

        return back();
    }
}

