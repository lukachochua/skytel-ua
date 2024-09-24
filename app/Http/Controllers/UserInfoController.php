<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserInfoController extends Controller
{
    public function showForm()
    {
        if (Auth::user()->is_info_provided) {
            return redirect()->route('dashboard');
        }

        return view('user-info');
    }

    public function submitForm(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|string',
            'address' => 'required|string',
        ]);

        $user = Auth::user();

        $user->userInfo()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'phone_number' => $request->phone_number,
                'address' => $request->address,
            ]
        );

        $user->update(['is_info_provided' => true]);

        return redirect()->route('dashboard');
    }
}
