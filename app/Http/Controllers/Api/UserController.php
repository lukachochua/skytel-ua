<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateUserRequest;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

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
}
