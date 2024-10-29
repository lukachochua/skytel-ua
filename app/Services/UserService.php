<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UserService
{
    public function loginOrRegisterUser(Request $request) // Ensure to type hint Request here
    {
        $user = User::where('email', $request['email'])->first();

        if ($user) {
            // User exists, log them in using the original request
            return $this->loginUser($request); // Pass the request instead of the user
        } else {
            // User does not exist, register them
            return $this->registerUser($request);
        }
    }

    public function loginUser(Request $request)
    {
        $userIp = $request->ip() ?? 'default-ip';
        $userAgent = $request->header('User-Agent') ?? 'default-user-agent';
        // Prepare headers
        $headers = [
            'Request-Id' => Str::uuid()->toString(),
            'Application-Id' => env('APP_ID'),
            'User-Ip' => $userIp,
            'User-Agent' => $userAgent,
        ];
dd($headers);

        // Prepare payload for login based on whether social login is being used

        if ($request->google_id || $request->facebook_id) {
            $payload = [
                'googleId' => $request->google_id,
                'facebookId' => $request->facebook_id,
            ];
        } else {
            $payload = [
                'email' => $request->email,
                'password' => $request->password,
            ];
        }

        Log::info('Preparing to log in user', ['payload' => $payload, 'headers' => $headers]);

        try {
            $response = Http::timeout(30)
                ->withHeaders($headers)
                ->post('http://198.18.22.87:8082/Customers/Login', $payload);

            Log::info('Received response from login API', [
                'status' => $response->status(),
                'body' => $response->successful() ? $response->json() : $response->body()
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'status' => $response->status(),
                    'body' => $response->json(),
                ];
            } else {
                Log::warning('Login API returned an unsuccessful response', [
                    'status' => $response->status(),
                    'body' => $response->json(),
                ]);

                return [
                    'success' => false,
                    'status' => $response->status(),
                    'body' => [
                        'error' => 'Login failed.',
                        'details' => $response->json(),
                    ],
                ];
            }
        } catch (\Exception $e) {
            Log::error('Exception during login API call', [
                'exception' => $e->getMessage(),
                'request_payload' => $payload,
                'headers' => $headers
            ]);
            return [
                'success' => false,
                'status' => 500,
                'body' => ['error' => $e->getMessage()],
            ];
        }
    }


    public function registerUser(Request $request)
    {
        $headers = [
            'Request-Id' => Str::uuid()->toString(),
            'Application-Id' => env('APP_ID'),
            'User-Ip' => $request->ip(),
            'User-Agent' => $request->header('User-Agent'),
        ];

        // Prepare payload for registration
        $payload = [
            'email' => $request->email,
            'password' => $request->password,
            'googleId' => $request->google_id,
            'facebookId' => $request->facebook_id,
            'avatarUrl' => $request->avatar,
        ];

        Log::info('Preparing to register user', ['payload' => $payload, 'headers' => $headers]);

        try {
            $response = Http::timeout(30)
                ->withHeaders($headers)
                ->post('http://198.18.22.87:8082/Customers/Registrate', $payload);

            Log::info('Received response from registration API', [
                'status' => $response->status(),
                'body' => $response->successful() ? $response->json() : $response->body()
            ]);

            return [
                'success' => $response->successful(),
                'status' => $response->status(),
                'body' => $response->json(),
                // 'redirect' => route('dashboard')
            ];
        } catch (\Exception $e) {
            Log::error('Exception during registration API call', [
                'exception' => $e->getMessage(),
                'request_payload' => $payload,
                'headers' => $headers
            ]);
            return [
                'success' => false,
                'status' => 500,
                'body' => ['error' => $e->getMessage()],
            ];
        }
    }

    public function getUserByEmail($email)
    {
        return User::where('email', $email)->first();
    }
}
