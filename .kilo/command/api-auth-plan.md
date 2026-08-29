# Plan: API Service & Authentication

## Overview
Build a backend-for-frontend (BFF) layer that authenticates admin users against the external API server, stores their per-user bearer token in the Laravel session, and proxies product/user data requests from the Vue.js frontend.

## Current State
- Laravel 13 + Vue 3 SPA (catch-all route → `app.blade.php`)
- Local `users` table exists; no auth controllers or routes yet
- `API_SERVER` already present in `.env` / `.env.example`
- Pest is the test runner

## Proposed Architecture

### 1. Configuration
- Add `api` service config in `config/services.php`:
  - `api_server` => `env('API_SERVER')`
  - `token_url` => `env('API_TOKEN_URL', env('API_SERVER') . '/token')`
  - `me_url` => `env('API_ME_URL', env('API_SERVER') . '/me')`

### 2. Service Layer
Create `app/Services/ApiService.php`:
- Uses `Illuminate\Support\Facades\Http`
- ` authenticate(string $email, string $password): array`
  - POST to `/token` with `email` and `password`
  - On HTTP 200 + `success.token`, return `['token' => ..., 'user' => ...]`
  - On failure, throw `AuthenticationException`
- ` me(string $token): array`
  - GET `/me` with `Authorization: Bearer {token}`
- ` get(string $endpoint, array $params = []): array`
  - Generic GET helper for future endpoints (products, etc.)
- ` post(string $endpoint, array $payload = []): array`
  - Generic POST helper
- All methods read the base URL from `config('services.api.api_server')`

### 3. Token Storage Strategy
Store the API bearer token **in the Laravel session**, keyed by the local user ID:
- `session(['api_token' => $token])`
- On logout, `session()->forget('api_token')`
- Rationale: token is ephemeral (per login), no DB migration required, automatically isolated per browser session.

### 4. Authentication Flow
Create `app/Http/Controllers/AuthController.php`:

**POST /login**
1. Validate `email` and `password`
2. Call `ApiService::authenticate($email, $password)`
3. If API auth succeeds:
   - Find local user by `email`; if missing, create one (name from API `/me` or email prefix)
   - Store token in session: `session(['api_token' => $token])`
   - Call `ApiService::me($token)` to sync user details
   - `Auth::login($user)`
   - Return JSON success (SPA-friendly)
4. If API auth fails:
   - Return JSON 401 error

**POST /logout**
1. `session()->forget('api_token')`
2. `Auth::logout()`
3. Return JSON success

### 5. Middleware
Create `app/Http/Middleware/Authenticated.php`:
- Extend `Illuminate\Auth\Middleware\Authenticate` or create custom
- Check `Auth::check()` — if not authenticated, redirect/login response
- For API routes, return 401 JSON

Create `app/Http/Middleware/WithApiToken.php` (optional, for future use):
- Ensures `session('api_token')` exists before proxying API calls

### 6. Routes
Add to `routes/web.php`:
- `POST /api/login` → `AuthController@login`
- `POST /api/logout` → `AuthController@logout`
- `GET /api/me` → returns current authenticated user + API user data
- Add auth middleware group for protected API routes

### 7. Frontend Integration Points
- Vue app will POST to `/api/login` with email/password
- On success, Laravel sets session cookie; Vue router navigates to dashboard
- Subsequent data requests from Vue → Laravel proxy routes → external API
- Example proxy route: `GET /api/products` → `ApiService::get('products')`

### 8. Tests
- `tests/Feature/AuthTest.php`
  - Successful login with valid API credentials (mock HTTP)
  - Failed login with invalid credentials
  - Logout clears session token
- `tests/Unit/ApiServiceTest.php`
  - `authenticate()` builds correct request and handles response
  - `me()` attaches bearer header
  - Error handling for non-200 responses

## Files to Create/Modify
| File | Action |
|------|--------|
| `config/services.php` | Add `api` service config |
| `app/Services/ApiService.php` | **Create** — external API client |
| `app/Http/Controllers/AuthController.php` | **Create** — login/logout |
| `app/Http/Middleware/Authenticated.php` | **Create** — auth guard |
| `routes/web.php` | Add auth + proxy API routes |
| `tests/Feature/AuthTest.php` | **Create** |
| `tests/Unit/ApiServiceTest.php` | **Create** |

## Sequencing
1. Config + `ApiService`
2. `AuthController` + routes
3. Middleware
4. Tests
5. Verify with `php artisan test` and `vendor/bin/pint`
