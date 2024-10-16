<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;


class ApiTestController extends Controller
{
    public function testApiConnection(Request $request)
    {
        $apiResponse = $this->sendRequestToApi($request);

        return response()->json([
            'message' => 'API request completed',
            'request' => [
                'url' => 'http://198.18.22.87:8082/Home/Test',
                'method' => 'GET',
                'headers' => $apiResponse['headers'],
            ],
            'response' => [
                'status' => $apiResponse['status'],
                'body' => $apiResponse['body'],
            ],
            'success' => $apiResponse['success'],
        ], $apiResponse['success'] ? 200 : 500);
    }

    private function sendRequestToApi(Request $request): array
    {
        $headers = [
            'Request-Id' => Str::uuid()->toString(),
            'Application-Id' => '5CAEC5D7-A97C-464B-8863-0F182902702E',
            'User-Ip' => $request->ip(),
            'User-Agent' => $request->header('User-Agent'),
        ];

        try {
            $response = Http::timeout(30)
                ->withHeaders($headers)
                ->get('http://198.18.22.87:8082/Home/Test');

            $success = $response->successful();
            $status = $response->status();
            $body = $response->body();

            if (!$success) {
                Log::error('Failed to send request to API', [
                    'status' => $status,
                    'response' => $body,
                ]);
            } else {
                Log::info('Successfully sent request to API', [
                    'status' => $status,
                    'response' => $body,
                ]);
            }

            return [
                'success' => $success,
                'status' => $status,
                'body' => $body,
                'headers' => $headers,
            ];
        } catch (\Exception $e) {
            Log::error('Exception occurred while sending request to API', [
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'status' => 500,
                'body' => $e->getMessage(),
                'headers' => $headers,
            ];
        }
    }
}
