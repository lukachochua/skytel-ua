<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            return redirect()->route('dashboard');
        }

        return redirect()->route('login')->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }


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
                    'is_info_provided' => false,
                    'auth_type' => 'google'
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


    // Facebook Methods
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

            $accessToken = $facebookUser->token;
            $avatarUrl = "https://graph.facebook.com/{$facebookUser->getId()}/picture?type=large&redirect=false&access_token={$accessToken}";

            $avatarData = file_get_contents($avatarUrl);
            $avatarDataJson = json_decode($avatarData, true);
            $user = User::where('email', $facebookUser->getEmail())->first();
            if (isset($avatarDataJson['data']['url'])) {
                $avatarUrl = $avatarDataJson['data']['url'];
            }
            if ($user) {
                Auth::login($user);
            } else {
                $newUser = User::create([
                    'name' => $facebookUser->getName(),
                    'email' => $facebookUser->getEmail(),
                    'facebook_id' => $facebookUser->getId(),
                    'avatar' => $avatarUrl,
                    'password' => bcrypt('facebook_oauth_password'),
                    'is_info_provided' => false,
                    'auth_type' => 'facebook',
                ]);
                Auth::login($newUser);
            }

            if (Auth::user()->is_info_provided) {
                return redirect()->route('dashboard');
            } else {
                return redirect()->route('user.info.form');
            }
        } catch (Exception $e) {
            Log::error('Facebook login error: ' . $e->getMessage());
            return redirect('/')->with('error', 'Unable to login using Facebook. Please try again.');
        }
    }


    // Password Reset Methods
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }


    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with(['status' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    }


    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.passwords.reset')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }


    // private function sendLoginInfoToApi(Request $request): void
    // {
    //     $headers = [
    //         'X-User-IP' => $request->ip(),
    //         'X-User-Browser' => $request->header('User-Agent'),
    //         'X-App-'
    //     ];

    //     $response = Http::withHeaders($headers)->post('https://example-api.com/endpoint', [
    //         'email' => Auth::user()->email,
    //         'name' => Auth::user()->name,
    //         'login_time' => now(),
    //     ]);

    //     if (!$response->successful()) {
    //         Log::error('Failed to send user login info to API', [
    //             'response' => $response->body(),
    //         ]);
    //     }
    // }
}
