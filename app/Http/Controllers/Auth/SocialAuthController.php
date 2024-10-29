<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Services\UserService;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\User;

class SocialAuthController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    // Redirect to Google/Facebook provider
    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->stateless()->redirect();
    }

    // Handle callback from provider
    public function handleProviderCallback($provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->stateless()->user();

            // Check if the user already exists
            $user = User::where('email', $socialUser->getEmail())->first();

            if (!$user) {
                // Register the user if they don't exist
                $request = new Request([
                    'email' => $socialUser->getEmail(),
                    'name' => $socialUser->getName(),
                    'avatar' => $socialUser->getAvatar(),
                    'social_id' => $socialUser->getId(),
                ]);

                $this->userService->registerUser($request);
            }

            // Now log the user in
            return $this->userService->loginUser(new Request(['social_id' => $socialUser->getId()]));
        } catch (\Exception $e) {
            Log::error('Social login error', ['exception' => $e->getMessage()]);
            return response()->json(['error' => 'Could not authenticate using ' . $provider], 500);
        }
    }
}
