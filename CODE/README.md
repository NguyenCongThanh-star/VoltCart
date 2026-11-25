# CODE

Laravel 11 application of VoltCart (PHP 8.2+, MySQL 8).

- Installation and run instructions: [../DOCUMENTATION/SETUP.md](../DOCUMENTATION/SETUP.md)
- What each folder is for: [../DOCUMENTATION/guides/CODE_MAP.md](../DOCUMENTATION/guides/CODE_MAP.md)
- How to add a feature: [../DOCUMENTATION/guides/HOW_TO_ADD_FEATURE.md](../DOCUMENTATION/guides/HOW_TO_ADD_FEATURE.md)

| Folder | Content |
|---|---|
| `routes/` | URLs, one file per area: `shop.php`, `account.php`, `admin.php` |
| `app/Http/Controllers/` | Controllers in `Shop/`, `Account/`, `Admin/` |
| `app/Models/` | One class per database table |
| `app/Services/` | Business rules shared by all pages (pricing, cart, coupons, orders) |
| `resources/views/` | Blade templates in `layouts/`, `components/`, `shop/`, `account/`, `admin/` |
| `public/css`, `public/js`, `public/images` | Hand-written CSS, JavaScript and images (no npm build step) |
| `database/` | Migrations, seeders and factories |
