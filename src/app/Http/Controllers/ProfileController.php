<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\UpdateProfileRequest;
use App\Models\Item;
use App\Models\Purchase;

class ProfileController extends Controller
{
    public function mypage()
    {
        $user = Auth::user();

        $listedItems = $user->items;

        $user->load('purchases.item');
        $purchasedItems = $user->purchases->map->item;

        return view('profile.mypage', compact('user', 'listedItems', 'purchasedItems'));
    }

    public function editSetting()
    {
        $user = Auth::user();

        return view('profile.edit', compact('user'));
    }


    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    public function update(UpdateProfileRequest $request)
    {
        $user = Auth::user();

        if ($request->hasFile('profile_image')) {
            $path = $request->file('profile_image')->store('public/profile_images');
            $user->profile_image = str_replace('public/', 'storage/', $path);
        }

        $user->update([
            'username' => $request->username,
            'zip' => $request->zip,
            'address' => $request->address,
            'building' => $request->building,
            'profile_image' => $user->profile_image ?? $user->getOriginal('profile_image'),
        ]);

        return redirect()->route('items.index')->with('message', 'プロフィールを更新しました！');
    }
}
