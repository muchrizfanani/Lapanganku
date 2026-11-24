# LapanganKu Laravel

Laravel version of the Week 9–11 coursework, added beside the original PHP application. The original root-level PHP pages and JSON files remain available and are not changed by the importer.

## Requirements

- PHP 8.3+ with `pdo_mysql`
- Composer
- MySQL server

## Local setup

From the repository root:

1. Create a MySQL database named `lapanganku_laravel`.
2. Enter this directory and install dependencies:

   ```sh
   cd laravel
   composer install
   cp .env.example .env
   php artisan key:generate
   ```

3. Set `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD` in `.env` to match the local MySQL account. Generate a JWT secret with `php -r "echo bin2hex(random_bytes(32)), PHP_EOL;"` and put the result in `.env` as `JWT_SECRET`. Do not commit `.env`.
4. Run the migrations and import a copy of the existing venue and booking records:

   ```sh
   php artisan migrate
   php artisan app:import-legacy-data
   ```

   The importer is repeatable: re-running it updates the database copies to match the JSON source. It reads `../data/venues.json` and `../data/bookings.json`; it does not edit or delete those source files.
5. Create an owner account interactively with Tinker:

   ```sh
   php artisan tinker
   ```

   ```php
   \App\Models\User::create([
       'name' => 'Pemilik Venue',
       'email' => 'pemilik@example.test',
       'password' => \Illuminate\Support\Facades\Hash::make('choose-a-local-password'),
       'role' => 'pemilik',
   ]);
   ```

6. Start the Laravel app:

   ```sh
   php artisan serve
   ```

   Open `http://127.0.0.1:8000/login` to access venue management. The original app remains in the repository root and can be served separately.

## Coursework features

- **Week 9:** MySQL migrations, Eloquent `User`, `Venue`, and `Booking` models, relationships, and full Venue CRUD.
- **Week 10:** Controller-based web routes, request validation, REST API resources, JSON responses, and standard HTTP status codes.
- **Week 11:** `POST /api/login` issues a signed JWT. All `/api/venues` and `/api/bookings` resource endpoints require `Authorization: Bearer <token>`; missing, expired, or invalid tokens receive `401`.

The login body is JSON with `email` and `password`. Venue API writes require a user with the `pemilik` role. Booking API creation derives the price from the selected venue rather than trusting a client-supplied total.

## Tests

Feature tests use an isolated in-memory SQLite database and a test-only JWT secret; they do not connect to or reset the MySQL development database.

```sh
php artisan test
```
