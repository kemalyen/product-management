<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function __construct(private ApiService $api) {}

    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        try {
            $credentials = $this->api->authenticate(
                $request->string('email'),
                $request->string('password')
            );

            $token = $credentials['token'];

            $apiUser = $this->api->me($token);

            $request->session()->put('api_token', $token);
            $request->session()->put('user', $apiUser);

            return response()->json([
                'success' => true,
                'user' => $request->session()->get('user'),
                'api_token' => $token,
            ]);
        } catch (\Throwable $e) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        $request->session()->forget('api_token');
        $request->session()->forget('user');

        return response()->json([
            'success' => true,
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'user' => $request->session()->get('user'),
            'api_token' => $request->session()->get('api_token'),
        ]);
    }
}
