<?php

use App\Services\ApiService;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    Http::preventStrayRequests();
});

test('authenticate returns token on success', function () {
    Http::fake([
        config('services.api.token_url') => Http::response([
            'success' => [
                'token' => '10|wvUr15MY0rSAOXuPJUK3YJPqMMuHw4Cgr74OaSsEa783a8e1',
            ],
        ], 200),
    ]);

    $service = new ApiService;

    $result = $service->authenticate('john@example.com', 'password');

    expect($result)->toBe([
        'token' => '10|wvUr15MY0rSAOXuPJUK3YJPqMMuHw4Cgr74OaSsEa783a8e1',
    ]);

    Http::assertSent(fn (Request $request) => $request->url() === config('services.api.token_url')
        && $request->method() === 'POST'
        && $request->data() === ['email' => 'john@example.com', 'password' => 'password']
    );
});

test('authenticate throws on failure', function () {
    Http::fake([
        config('services.api.token_url') => Http::response([
            'message' => 'Unauthorized',
        ], 401),
    ]);

    $service = new ApiService;

    expect(fn () => $service->authenticate('john@example.com', 'wrong'))
        ->toThrow(RequestException::class);
});

test('me returns user data with bearer token', function () {
    Http::fake([
        config('services.api.me_url') => Http::response([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ], 200),
    ]);

    $service = new ApiService;

    $result = $service->me('fake-token');

    expect($result)->toBe([
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ]);

    Http::assertSent(fn (Request $request) => $request->url() === config('services.api.me_url')
        && $request->method() === 'GET'
        && $request->hasHeader('Authorization')
        && $request->header('Authorization')[0] === 'Bearer fake-token'
    );
});

test('get proxies request to api server', function () {
    Http::fake([
        config('services.api.server').'/products' => Http::response([
            ['id' => 1, 'name' => 'Product 1'],
        ], 200),
    ]);

    $service = new ApiService;

    $result = $service->get('products');

    expect($result)->toBe([
        ['id' => 1, 'name' => 'Product 1'],
    ]);

    Http::assertSent(fn (Request $request) => $request->url() === config('services.api.server').'/products'
        && $request->method() === 'GET'
    );
});

test('post proxies request to api server', function () {
    Http::fake([
        config('services.api.server').'/products' => Http::response([
            'id' => 1,
            'name' => 'New Product',
        ], 201),
    ]);

    $service = new ApiService;

    $result = $service->post('products', ['name' => 'New Product']);

    expect($result)->toBe([
        'id' => 1,
        'name' => 'New Product',
    ]);

    Http::assertSent(fn (Request $request) => $request->url() === config('services.api.server').'/products'
        && $request->method() === 'POST'
        && $request->data() === ['name' => 'New Product']
    );
});
