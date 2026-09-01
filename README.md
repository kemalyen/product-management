# Product Admin

A Laravel 13 + Vue.js 3 single-page application (SPA) admin panel that connects to an external Product API. This application does not use a local database; all data is proxied from the remote API server.

## How It Works

This admin panel acts as a **backend-for-frontend (BFF)** layer between the Vue.js frontend and the external Product API.

The API source code can be found at https://github.com/kemalyen/product-api

### Authentication Flow

1. User submits credentials (email/password) to `/api/login`
2. Laravel proxies the credentials to `{{API_SERVER}}/token`
3. On success, the API returns a bearer token
4. Laravel stores the token in the session and fetches user details from `{{API_SERVER}}/me`
5. The user's role and account information are stored in the session
6. Subsequent API requests use the stored bearer token to proxy data from the external API

### Session Management

- Authentication state is maintained via Laravel session cookies
- The API bearer token is stored server-side in the session
- No local user database is used; user data comes from the remote API

## Features

- **Authentication**: Login/logout against external API with bearer token storage
- **Role-Based Access Control**: Features gated by user role (e.g., `Account Admin` can manage users)
- **Products**: Browse products with filters and pagination
  - Filter by name, SKU, barcode, status, stock, and price
  - Paginated results from the API
- **Users**: View, create, edit, and delete users (requires `Account Admin` role)
  - Assign roles during user creation/editing
  - Available roles: `Account Admin`, `Account User`, `Account Api User`
- **Profile**: View current user details including role and account information
- **Responsive UI**: Mobile-friendly navigation with Tailwind CSS

## Requirements

- PHP 8.3+
- Composer
- Node.js & npm
- External Product API server

## Installation

1. **Clone the repository**
   ```bash
   git clone [https://github.com/kemalyen/product-management.git](https://github.com/kemalyen/product-management.git) product-admin
   cd product-admin
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node dependencies**
   ```bash
   npm install
   ```

4. **Configure environment**
   ```bash
   cp .env.example .env
   ```
   
   Update the following variables in `.env`:
   ```env
   APP_URL=http://localhost:8000
   API_SERVER=http://your-api-server.com
   API_VERSION=v1
   ```

5. **Generate application key**
   ```bash
   php artisan key:generate
   ```

6. **Build frontend assets**
   ```bash
   npm run build
   ```

7. **Start the development server**
   ```bash
   php artisan serve
   ```

8. **Access the application**
   
   Navigate to `http://localhost:8000` in your browser.

## Development

Run the development server with Vite hot reloading:

```bash
composer run dev
```

This starts the Laravel development server, queues worker, logs, and Vite dev server simultaneously.

## Testing

Run the test suite with Pest:

```bash
vendor/bin/pest
```

Run specific test files:

```bash
vendor/bin/pest tests/Feature/AuthTest.php
vendor/bin/pest tests/Unit/ApiServiceTest.php
```

## API Configuration

The application expects the following endpoints from the Product API:

| Endpoint | Purpose |
|----------|---------|
| `{{API_SERVER}}/token` | Exchange email/password for bearer token |
| `{{API_SERVER}}/me` | Get current authenticated user details |
| `{{API_SERVER}}/{{version}}/products` | List products with pagination and filters |
| `{{API_SERVER}}/{{version}}/users` | List users with pagination |
| `{{API_SERVER}}/{{version}}/users/{id}` | Get single user |
| `{{API_SERVER}}/{{version}}/users` (POST) | Create user |
| `{{API_SERVER}}/{{version}}/users/{id}` (PUT) | Update user |
| `{{API_SERVER}}/{{version}}/users/{id}` (DELETE) | Delete user |

## Environment Variables

| Variable | Description | Default |
|----------|-------------|---------|
| `API_SERVER` | Base URL of the Product API | Required |
| `API_VERSION` | API version prefix | `v1` |
| `API_TOKEN_URL` | Token endpoint path | `{{API_SERVER}}/token` |
| `API_ME_URL` | Me endpoint path | `{{API_SERVER}}/me` |

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
