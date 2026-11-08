# Setup Instructions

> Draft – will be completed when the Laravel application is in place.

## Requirements

- PHP 8.2+
- Composer
- MySQL 8
- Git

Recommended on Windows: [Laragon](https://laragon.org/) (includes PHP, Composer and MySQL).

## Steps (planned)

1. Clone the repository and open the `CODE` folder.
2. Run `composer install`.
3. Copy `.env.example` to `.env` and set the database connection.
4. Run `php artisan key:generate`.
5. Run `php artisan migrate --seed` to create tables and sample data.
6. Run `php artisan storage:link`.
7. Run `php artisan serve` and open http://127.0.0.1:8000.

## Test accounts

Administrator and member test accounts will be listed here.
