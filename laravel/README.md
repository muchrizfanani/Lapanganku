# LapanganKu

The Laravel application is the main LapanganKu website and contains the public landing page, player booking flow and history, owner dashboard, and venue management. The original PHP files remain in the repository as a backup/reference; the Laravel site imports copies of the JSON records and never writes to those source files.

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
5. Create player and owner accounts interactively with Tinker:

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

   \App\Models\User::create([
       'name' => 'Pemain LapanganKu',
       'email' => 'pemain@example.test',
       'password' => \Illuminate\Support\Facades\Hash::make('choose-another-local-password'),
       'role' => 'pemain',
   ]);
   ```

6. Start the LapanganKu app from this directory:

   ```sh
   php artisan serve
   ```

   Open `http://127.0.0.1:8000` for the public landing page. Use **Masuk** or **Daftar** in the top navigation. Players can select venue slots and see their booking history; owners can manage venues and confirm or reject bookings from the dashboard. Use the Laravel server for the active website; the root PHP files are retained only as a backup/reference.

## Coursework features

- **Week 9:** MySQL migrations, Eloquent `User`, `Venue`, and `Booking` models, relationships, and full Venue CRUD.
- **Week 10:** Controller-based web routes, request validation, REST API resources, JSON responses, and standard HTTP status codes.
- **Week 11:** `POST /api/login` issues a signed JWT. All `/api/venues` and `/api/bookings` resource endpoints require `Authorization: Bearer <token>`; missing, expired, or invalid tokens receive `401`.
- **LapanganKu workflows:** Player and owner registration/login, slot booking with conflict checks, player booking history, owner booking approval, and owner venue management.

The login body is JSON with `email` and `password`. Venue API writes require a user with the `pemilik` role. Booking API creation derives the price from the selected venue rather than trusting a client-supplied total.

## Tests

Feature tests use an isolated in-memory SQLite database and a test-only JWT secret; they do not connect to or reset the MySQL development database.

```sh
php artisan test
```
