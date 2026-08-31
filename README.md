# Nuttime

Multilingual Nuttime corporate catalogue built with Laravel 13, Blade, Tailwind CSS, Alpine.js, Vite and Filament 5.

## Local setup

Requirements: PHP 8.5+, Composer, Node.js 20+, npm. Run `composer install`, copy `.env.example` to `.env`, set `APP_URL`, run `php artisan key:generate`, `php artisan migrate`, `php artisan storage:link`, `npm install`, and `npm run build`. SQLite works by default; for MySQL set `DB_CONNECTION=mysql` and the normal `DB_*` variables in `.env`.

Create an administrator manually; no credentials are seeded: `php artisan make:filament-user`. The panel is available at `/admin`. Supported locales are `tr`, `en`, and `de`; Turkish is the default and does not require a prefix.

Run tests with `php artisan test`, check formatting with `vendor/bin/pint --test`, and build frontend assets with `npm run build`. `.env` is intentionally untracked.
