<?php

use Illuminate\Support\Facades\Http;

test('user can login with valid api credentials', function () {
    Http::preventStrayRequests();

    Http::fake([
        config('services.api.token_url') => Http::response([
            'success' => [
                'token' => '10|wvUr15MY0rSAOXuPJUK3YJPqMMuHw4Cgr74OaSsEa783a8e1',
            ],
        ], 200),
        config('services.api.me_url') => Http::response([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ], 200),
    ]);

    $response = $this->post('/api/login', [
        'email' => 'john@example.com',
        'password' => 'password',
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
        ]);

    $this->assertEquals('10|wvUr15MY0rSAOXuPJUK3YJPqMMuHw4Cgr74OaSsEa783a8e1', session('api_token'));
    $this->assertEquals('John Doe', session('user.name'));
});

test('login fails with invalid credentials', function () {
    Http::preventStrayRequests();

    Http::fake([
        config('services.api.token_url') => Http::response([
            'message' => 'Unauthorized',
        ], 401),
    ]);

    $response = $this->post('/api/login', [
        'email' => 'john@example.com',
        'password' => 'wrong-password',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors('email');

    $this->assertNull(session('user'));
});

test('authenticated user can logout', function () {
    $response = $this->withSession([
        'api_token' => 'fake-token',
        'user' => ['name' => 'John Doe', 'email' => 'john@example.com'],
    ])->post('/api/logout');

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
        ]);

    $this->assertNull(session('user'));
});

test('authenticated user can get their info', function () {
    $response = $this->withSession([
        'api_token' => 'fake-token',
        'user' => ['name' => 'John Doe', 'email' => 'john@example.com'],
    ])->get('/api/me');

    $response->assertStatus(200)
        ->assertJson([
            'user' => [
                'name' => 'John Doe',
                'email' => 'john@example.com',
            ],
            'api_token' => 'fake-token',
        ]);
});

test('unauthenticated user cannot access protected routes', function () {
    $response = $this->get('/api/me');

    $response->assertStatus(401)
        ->assertJson([
            'success' => false,
            'message' => 'Unauthenticated.',
        ]);
});
