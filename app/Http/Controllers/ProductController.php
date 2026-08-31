<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(private ApiService $api) {}

    public function index(Request $request): JsonResponse
    {
        $token = $request->session()->get('api_token');

        $params = $request->query();

        if (array_key_exists('page', $params)) {
            $params['page'] = (int) $params['page'];
        }

        $data = $this->api->get(config('services.api.version').'/'.'products', $params, $token);

        return response()->json($data);
    }
}
