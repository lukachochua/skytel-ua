<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserInfoController extends Controller
{
    // Show the form for collecting additional information
    public function showForm()
    {
        $user = Auth::user();

        if ($user->is_info_provided) {
            return redirect()->route('dashboard');
        }

        return view('user-info');
    }

    // Handle the submission of the user info form
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
