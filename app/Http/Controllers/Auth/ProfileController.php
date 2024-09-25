<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserInfo;

class ProfileController extends Controller
{
    public function showProfile()
    {
        $user = Auth::user();
        $userInfo = $user->userInfo;
        return view('profile', compact('user', 'userInfo'));
    }


    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:15',
            'address' => 'nullable|string|max:255',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', 
        ]);

        $user = Auth::user();
        $user->name = $request->name;

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public'); 
            $user->avatar = $path; 
        }

        $user->save();

        if ($user->userInfo) {
            $user->userInfo->update([
                'phone_number' => $request->phone_number,
                'address' => $request->address,
            ]);
        } else {
            UserInfo::create([
                'user_id' => $user->id,
                'phone_number' => $request->phone_number,
                'address' => $request->address,
            ]);
        }

        return redirect()->route('profile')->with('success', 'Profile updated successfully!');
    }
}
