<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\LoginRequest;
use App\Services\UserService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
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
        return Socialite::driver($provider)->stateless()->redirect();
    }

    public function handleProviderCallback($provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->stateless()->user();

            // Create a new Request instance with the social data
            $request = new \Illuminate\Http\Request([
                'email' => $socialUser->getEmail(),
                'avatar' => $socialUser->getAvatar(),
                $provider . '_id' => $socialUser->getId(),
                'password' => null,  // Default values handled in UserService
                'password_confirmation' => null,
            ]);

            // Manually extract the User IP and User Agent from the current request
            $request->headers->set('User-Ip', $this->getUserIp());
            $request->headers->set('User-Agent', $this->getUserAgent());

            Log::info('Handling registration with social provider', ['provider' => $provider, 'user' => $socialUser]);

            $result = $this->userService->registerUser($request);

            if ($result['success']) {
                return response()->json([
                    'message' => 'User successfully registered via ' . ucfirst($provider) . '!',
                    'data' => $result['body']
                ], $result['status']);
            }

            return response()->json([
                'error' => 'Registration via ' . ucfirst($provider) . ' failed!',
                'details' => $result['body']
            ], $result['status']);
        } catch (\Exception $e) {
            Log::error('Exception during social registration', ['exception' => $e->getMessage()]);
            return response()->json([
                'error' => 'Registration via ' . ucfirst($provider) . ' failed due to an exception!',
                'details' => $e->getMessage()
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
