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
        $tab = request()->query('tab', 'listed');
        $listedItems = $user->items()->latest()->get();
        $purchasedItems = $user->purchases()
            ->with('item')
            ->latest()
            ->get()
            ->pluck('item');

        return view('profile.mypage', compact('user', 'tab', 'listedItems', 'purchasedItems'));
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
            $path = $request->file('profile_image')->store('public/images/profiles');
            $user->profile_image = str_replace('public/', 'storage/', $path);
        }



        // 他のプロフィール情報の更新
        $user->username = $request->username;
        $user->zip = $request->zip;
        $user->address = $request->address;
        $user->building = $request->building;

        $user->save();

        return redirect()->route('items.index');
    }
}
