<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function __construct(private ApiService $api) {}

    public function index(Request $request): JsonResponse
    {
        $token = $request->session()->get('api_token');

        $params = $request->query();

        if (array_key_exists('page', $params)) {
            $params['page'] = (int) $params['page'];
        }

        $data = $this->api->get(config('services.api.version').'/'.'accounts', $params, $token);

        return response()->json($data);
    }

    public function show(Request $request, string $id): JsonResponse
    {
        $token = $request->session()->get('api_token');

        $data = $this->api->get(config('services.api.version').'/'.'accounts/'.$id, [], $token);

        return response()->json($data);
    }

    public function store(Request $request): JsonResponse
    {
        $token = $request->session()->get('api_token');

        $payload = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'account_number' => ['required', 'digits_between:2,10'],
            'status' => ['required', 'string'],
        ]);

        $data = $this->api->post(config('services.api.version').'/'.'accounts', $payload, $token);

        return response()->json($data, 201);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $token = $request->session()->get('api_token');

        $payload = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'account_number' => ['sometimes', 'string', 'max:255'],
            'status' => ['sometimes', 'string'],
        ]);

        $data = $this->api->put(config('services.api.version').'/'.'accounts/'.$id, $payload, $token);

        return response()->json($data);
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        $token = $request->session()->get('api_token');

        $this->api->delete(config('services.api.version').'/'.'accounts/'.$id, $token);

        return response()->json(null, 204);
    }
}
