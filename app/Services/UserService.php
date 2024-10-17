<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UserService
{
    public function registerUser($request)
    {
        $headers = [
            'Request-Id' => Str::uuid()->toString(),
            'Application-Id' => '5CAEC5D7-A97C-464B-8863-0F182902702E',
            'User-Ip' => $request->ip(),
            'User-Agent' => $request->header('User-Agent'),
        ];

        $password = Str::random(16);

        $payload = [
            'email' => $request->email,
            'googleId' => $request->google_id,
            'facebookId' => $request->facebook_id,
            'password' => $request->password ?? $password,
            'passwordConfirm' => $request->password_confirmation ?? $password,
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
            ];
        } catch (\Exception $e) {
            Log::error('Exception during API call', [
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
}
