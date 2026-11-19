# Setup Instructions

> Work in progress – test accounts and sample data will be added when the seeders are ready.

## Requirements

- PHP 8.2+ (tested with PHP 8.3)
- Composer 2
- MySQL 8
- Git

Recommended on Windows: [Laragon](https://laragon.org/) (includes PHP, Composer and MySQL).
Start Laragon and click **Start All** so that MySQL is running, then use the Laragon terminal
(**Menu → Terminal**) for the commands below.

## Steps

1. Clone the repository and open the `CODE` folder:

   ```bash
   git clone https://github.com/NguyenCongThanh-star/VoltCart.git
   cd VoltCart/CODE
   ```

2. Install the PHP dependencies:

   ```bash
   composer install
   ```

3. Create the environment file and the application key:

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. Create an empty MySQL database named `voltcart` (HeidiSQL in Laragon, or the command below).
   The default `.env` uses user `root` with an empty password, which matches Laragon;
   change `DB_USERNAME` / `DB_PASSWORD` in `.env` if your MySQL is different.

   ```bash
   mysql -u root -e "CREATE DATABASE voltcart CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
   ```

5. Create the tables:

   ```bash
   php artisan migrate
   ```

6. Link the public storage folder (used for product images):

   ```bash
   php artisan storage:link
   ```

7. Start the application and open http://127.0.0.1:8000:

   ```bash
   php artisan serve
   ```

No Node.js or npm build step is needed: the CSS and JavaScript are hand-written files in
`CODE/public`.

## After pulling new code

```bash
composer install
php artisan migrate
```

## Test accounts

Administrator and member test accounts will be listed here.
