<?php

namespace App\Services;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

class ApiService
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.api.server'), '/');
    }

    public function authenticate(string $email, string $password): array
    {
        $response = Http::connectTimeout(3)
            ->withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'User-Agent' => 'PRODUCT-WEB-' . config('services.api.version'),
            ])
            ->timeout(10)
            ->post($this->url(config('services.api.token_url')), [
                'email' => $email,
                'password' => $password,
            ]);

        $data = $response->json();

        if ($response->successful() && isset($data['success']['token'])) {
            return [
                'token' => $data['success']['token'],
            ];
        }

        throw new RequestException($response);
    }

    public function me(string $token): array
    {
        $response = Http::connectTimeout(3)
            ->withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'User-Agent' => 'PRODUCT-WEB-' . config('services.api.version'),
            ])
            ->timeout(10)
            ->withToken($token)
            ->get($this->url(config('services.api.me_url')));

        return $response->throw()->json();
    }

    public function get(string $endpoint, array $params = [], ?string $token = null): array
    {
        $http = Http::connectTimeout(3)
            ->withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'User-Agent' => 'PRODUCT-WEB-' . config('services.api.version'),
            ])
            ->timeout(10);

        if ($token) {
            $http->withToken($token);
        }

        $response = $http->get($this->url($endpoint), $params);

        return $response->throw()->json();
    }

    public function post(string $endpoint, array $payload = [], ?string $token = null): array
    {
        $http = Http::connectTimeout(3)
            ->withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'User-Agent' => 'PRODUCT-WEB-' . config('services.api.version'),
            ])
            ->timeout(10);

        if ($token) {
            $http->withToken($token);
        }

        $response = $http->post($this->url($endpoint), $payload);

        return $response->throw()->json();
    }

    public function put(string $endpoint, array $payload = [], ?string $token = null): array
    {
        $http = Http::connectTimeout(3)

            ->withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'User-Agent' => 'PRODUCT-WEB-' . config('services.api.version'),
            ])
            ->timeout(10);

        if ($token) {
            $http->withToken($token);
        }

        $response = $http->put($this->url($endpoint), $payload);

        return $response->throw()->json();
    }

    public function delete(string $endpoint, ?string $token = null): ?array
    {
        $http = Http::connectTimeout(3)
            ->withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'User-Agent' => 'PRODUCT-WEB-' . config('services.api.version'),
            ])
            ->timeout(10);

        if ($token) {
            $http->withToken($token);
        }

        $response = $http->delete($this->url($endpoint));

        return $response->throw()->json();
    }

    private function url(string $path): string
    {
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return $this->baseUrl . '/' . ltrim($path, '/');
    }
}
