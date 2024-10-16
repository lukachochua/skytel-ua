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
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $this->sendRequestToApi($request);
            return redirect()->route('dashboard');
        }

        return redirect()->route('login')->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
            Log::info('Google user retrieved', ['user' => $googleUser]);

            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                Auth::login($user);
            } else {
                $newUser = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'password' => Hash::make('google_oauth_password'),
                    'is_info_provided' => false,
                    'auth_type' => 'google'
                ]);
                Auth::login($newUser);
                Log::info('User authenticated', ['user' => $newUser, 'is_authenticated' => Auth::check()]);
                Log::info('Session data', ['session' => session()->all()]);
            }

            return $this->redirectAfterLogin();
        } catch (Exception $e) {
            Log::error('Google login error: ' . $e->getMessage());
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
            $avatarUrl = $this->getFacebookAvatarUrl($facebookUser);

            $user = User::where('email', $facebookUser->getEmail())->first();
            if ($user) {
                Auth::login($user);
            } else {
                $newUser = User::create([
                    'name' => $facebookUser->getName(),
                    'email' => $facebookUser->getEmail(),
                    'facebook_id' => $facebookUser->getId(),
                    'avatar' => $avatarUrl,
                    'password' => Hash::make('facebook_oauth_password'),
                    'is_info_provided' => false,
                    'auth_type' => 'facebook',
                ]);
                Auth::login($newUser);
            }

            return $this->redirectAfterLogin();
        } catch (Exception $e) {
            Log::error('Facebook login error: ' . $e->getMessage());
            return redirect('/')->with('error', 'Unable to login using Facebook. Please try again.');
        }
    }

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

    private function redirectAfterLogin()
    {
        session(['login_success' => true]);


        Log::info('Session data after login', session()->all());

        return Auth::user()->is_info_provided
            ? redirect()->route('dashboard')
            : redirect()->route('user.info.form');
    }

    private function getFacebookAvatarUrl($facebookUser)
    {
        $accessToken = $facebookUser->token;
        $avatarUrl = "https://graph.facebook.com/{$facebookUser->getId()}/picture?type=large&redirect=false&access_token={$accessToken}";

        $avatarData = file_get_contents($avatarUrl);
        $avatarDataJson = json_decode($avatarData, true);

        return isset($avatarDataJson['data']['url']) ? $avatarDataJson['data']['url'] : '';
    }

    private function sendRequestToApi(Request $request): void
    {
        $headers = [
            'Request-Id' => Str::uuid()->toString(),
            'Application-Id' => '9E8E475C-94AF-4AFB-BCDA-99D578E3E674',
            'User-Ip' => $request->ip(),
            'User-Agent' => $request->header('User-Agent'),
        ];

        $data = [
            'email' => $request->email,
            'password' => $request->password,
            'timestamp' => now(),
        ];

        try {
            $response = Http::withHeaders($headers)
                ->post('http://198.18.22.87:8082/Home/Test', $data);

            if (!$response->successful()) {
                Log::error('Failed to send request to API', [
                    'status' => $response->status(),
                    'response' => $response->body(),
                ]);
            } else {
                Log::info('Successfully sent request to API', [
                    'status' => $response->status(),
                    'response' => $response->body(),
                ]);
            }
        } catch (Exception $e) {
            Log::error('Exception occurred while sending request to API', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
