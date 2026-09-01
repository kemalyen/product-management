<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function __construct(private ApiService $api) {}

    public function index(Request $request): JsonResponse
    {
        $token = $request->session()->get('api_token');

        $params = $request->query();

        if (array_key_exists('page', $params)) {
            $params['page'] = (int) $params['page'];
        }


        $data = $this->api->get(config('services.api.version') . '/' . 'users', $params, $token);

        return response()->json($data);
    }

    public function show(Request $request, string $id): JsonResponse
    {
        $token = $request->session()->get('api_token');

        $data = $this->api->get(config('services.api.version') . '/' . 'users/' . $id, [], $token);

        return response()->json($data);
    }

    public function store(Request $request): JsonResponse
    {
        $token = $request->session()->get('api_token');

        $payload = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'string'],
            'account_id' => ['nullable', 'integer'],
        ]);

        $user = $request->session()->get('user');

        if (empty($payload['account_id']) || ! $this->isAdmin($user)) {
            $payload['account_id'] = $user['data']['includes']['id'];
        }

        $data = $this->api->post(config('services.api.version') . '/' . 'users', $payload, $token);

        return response()->json($data, 201);
    }

    private function isAdmin(array $user): bool
    {
        return isset($user['data']['relationships']['roles']['data']['name'])
            && $user['data']['relationships']['roles']['data']['name'] === 'Admin';
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $token = $request->session()->get('api_token');

        $payload = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'max:255'],
            'password' => ['sometimes', 'string', 'min:8'],
            'role' => ['sometimes', 'string'],
        ]);

        $data = $this->api->put(config('services.api.version') . '/' . 'users/' . $id, $payload, $token);

        return response()->json($data);
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        $token = $request->session()->get('api_token');

        $this->api->delete(config('services.api.version') . '/' . 'users/' . $id, $token);

        return response()->json(null, 204);
    }
}
