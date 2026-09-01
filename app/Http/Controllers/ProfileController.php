<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function __construct(private ApiService $api) {}

    public function update(Request $request): JsonResponse
    {
        $token = $request->session()->get('api_token');
        $user = $request->session()->get('user');

        $id = $user['data']['id'] ?? null;

        if (! $id) {
            return response()->json([
                'success' => false,
                'message' => 'User not found in session.',
            ], 404);
        }

        $payload = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
        ]);

        $data = $this->api->put(config('services.api.version').'/'.'users/'.$id, $payload, $token);

        $request->session()->put('user', $data);

        return response()->json($data);
    }
}
