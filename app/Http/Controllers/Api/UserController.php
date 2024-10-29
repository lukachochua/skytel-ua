<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\LoginRequest;
use App\Services\UserService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Cookie;


class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function register(CreateUserRequest $request): JsonResponse
    {
        Log::info('Register User Request', ['data' => $request->all()]);

        try {
            Log::info('Calling UserService to register user');

            $result = $this->userService->registerUser($request);

            if ($result['success']) {
                Log::info('UserService registration success', ['response' => $result]);
                return response()->json([
                    'message' => 'User successfully registered!',
                    'data' => $result['body']
                ], $result['status']);
            }

            Log::error('UserService registration failed', ['response' => $result]);
            return response()->json([
                'error' => 'Registration failed!',
                'details' => $result['body']
            ], $result['status']);
        } catch (\Exception $e) {
            Log::error('Exception occurred during registration', ['exception' => $e->getMessage()]);
            return response()->json([
                'error' => 'Registration failed due to an exception!',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function redirectToProvider($provider)
    {
        Log::info('Before redirect to Google:', [request()->cookies->all()]);

        return Socialite::driver($provider)->stateless()->redirect();
    }

    public function handleProviderCallback($provider)
    {
        Log::info('After redirect from Google:', [request()->cookies->all()]);

        try {
            // Retrieve user info from the provider
            $socialUser = Socialite::driver($provider)->stateless()->user();

            // Log the social user details
            Log::info('Social user information', [
                'provider' => $provider,
                'socialUser' => [
                    'email' => $socialUser->getEmail(),
                    'name' => $socialUser->getName(),
                    'google_id' => $provider === 'google' ? $socialUser->getId() : null,
                    'facebook_id' => $provider === 'facebook' ? $socialUser->getId() : null,
                    'avatar' => $socialUser->getAvatar(),
                    'access_token' => $socialUser->token,
                    'refresh_token' => $socialUser->refreshToken,
                ],
            ]);

            // Prepare data for login or registration
            $request = new Request([
                'email' => $socialUser->getEmail(),
                'name' => $socialUser->getName(),
                'google_id' => $provider === 'google' ? $socialUser->getId() : null,
                'facebook_id' => $provider === 'facebook' ? $socialUser->getId() : null,
                'avatar' => $socialUser->getAvatar(),
                'access_token' => $socialUser->token,
                'refresh_token' => $socialUser->refreshToken,
            ]);

            // Delegate to UserService for login or registration
            $result = $this->userService->loginOrRegisterUser($request);

            // Log the result for debugging
            Log::info('Result from UserService', ['result' => $result]);

            // Handle failed authentication more specifically
            if (!$result['success']) {
                return response()->json([
                    'error' => $result['body']['error'] ?? 'Authentication failed.',
                    'details' => $result['body'] ?? 'An unknown error occurred',
                ], 401);
            }

            Log::info('Access token set in cookies', ['access_token' => $result['body']['data']['accessToken']]);
            Log::info('Refresh token set in cookies', ['refresh_token' => $result['body']['data']['refreshToken']]);

            // Set tokens in cookies
            return redirect()->route('dashboard')
                ->withCookie(cookie('access_token', $result['body']['data']['accessToken'], 60))
                ->withCookie(cookie('refresh_token', $result['body']['data']['refreshToken'], 60 * 24));
        } catch (\Exception $e) {
            Log::error('Social login error', ['exception' => $e->getMessage()]);

            return response()->json([
                'error' => 'Could not authenticate using ' . $provider,
                'details' => $e->getMessage(),
            ], 500);
        }
    }


    protected function getUserIp()
    {
        return request()->getClientIp();
    }

    protected function getUserAgent()
    {
        return request()->header('User-Agent');
    }

    /**
     * Handle user login.
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request)
    {
        Log::info('Login User Request', ['data' => $request->all()]);

        try {
            Log::info('Calling UserService to log in user');
            $result = $this->userService->loginUser($request);

            if ($result['success']) {
                Log::info('UserService login success', ['response' => $result]);

                // Extract the token and other necessary data
                $accessToken = $result['body']['data']['accessToken'];
                $refreshToken = $result['body']['data']['refreshToken'];

                // Convert the accessTokenExpirationTime to numeric format
                $expirationTime = Carbon::parse($result['body']['data']['accessTokenExpirationTime']);
                $tokenExpirationInMinutes = Carbon::now()->diffInMinutes($expirationTime);

                // Set tokens in secure, HTTP-only cookies (valid for tokenExpirationInMinutes)
                Cookie::queue(Cookie::make('accessToken', $accessToken, $tokenExpirationInMinutes, null, null, true, true, false, 'Strict'));
                Cookie::queue(Cookie::make('refreshToken', $refreshToken, $tokenExpirationInMinutes, null, null, true, true, false, 'Strict'));

                // Redirect the user to the dashboard
                return redirect()->route('dashboard');
            }

            // Log the failed response
            Log::error('UserService login failed', [
                'response' => $result,
                'status' => $result['status'],
                'details' => $result['body'],
            ]);

            // Redirect back with error status
            return redirect()->back()->withErrors([
                'error' => 'Login failed!',
                'status' => $result['status'],
                'details' => $result['body'],
            ]);
        } catch (\Exception $e) {
            Log::error('Exception occurred during login', [
                'exception' => $e->getMessage(),
                'status' => 500
            ]);

            return redirect()->back()->withErrors([
                'error' => 'Login failed due to an exception!',
                'details' => $e->getMessage(),
                'status' => 500,
            ]);
        }
    }
}
