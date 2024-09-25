<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Exception;

class LoginController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from Google.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                Auth::login($user);
            } else {
                $newUser = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'password' => bcrypt('google_oauth_password'),
                    'is_info_provided' => false
                ]);
                Auth::login($newUser);
            }

            if (Auth::user()->is_info_provided) {
                return redirect()->route('dashboard');
            } else {
                return redirect()->route('user.info.form');
            }
        } catch (Exception $e) {
            return redirect('login')->with('error', 'Unable to login using Google. Please try again.');
        }
    }

    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')
            ->scopes(['email', 'public_profile', 'user_photos']) 
            ->stateless()
            ->redirect();
    }

    public function handleFacebookCallback()
    {
        try {
            $facebookUser = Socialite::driver('facebook')->stateless()->user();
            $user = User::where('email', $facebookUser->getEmail())->first();

            if ($user) {
                Auth::login($user);
            } else {
                $newUser = User::create([
                    'name' => $facebookUser->getName(),
                    'email' => $facebookUser->getEmail(),
                    'facebook_id' => $facebookUser->getId(),
                    'avatar' => $facebookUser->getAvatar(),
                    'password' => bcrypt('facebook_oauth_password'),
                    'is_info_provided' => false
                ]);
                Auth::login($newUser);
            }

            if (Auth::user()->is_info_provided) {
                return redirect()->route('dashboard');
            } else {
                return redirect()->route('user.info.form');
            }
        } catch (Exception $e) {
            return redirect('/')->with('error', 'Unable to login using Facebook. Please try again.');
        }
    }
}
