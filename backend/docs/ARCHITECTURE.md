# Architecture

Laravel 13 + Blade + Tailwind CSS + Alpine.js + Vite. Public routes are handled by `SiteController` while the locale middleware validates `tr`, `en`, and `de`. The catalog schema uses translation tables and soft deletes. Filament is installed at `/admin`; create the first administrator manually with `php artisan make:filament-user`.
