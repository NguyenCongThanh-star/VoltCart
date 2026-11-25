# VoltCart – Smartphones & Laptops Store

Web E-commerce project of team **WEB_2627302_10**.

VoltCart is an online store for smartphones, laptops and accessories. Customers can browse and
filter products, check out as guests without an account, and registered members get a lower
member price. Administrators manage products, categories, orders, coupons and sales reports.

## Team

| Member | Role |
|---|---|
| Nguyễn Công Thành | Team leader, backend core |
| Nguyễn Minh Mẫn | Customer frontend (HTML/CSS) |
| Lương Nhất Khôi | Administrator area |
| Huỳnh Nhật Hòa | Reports, accounts, QA & documentation |

Student IDs and detailed responsibilities: [DOCUMENTATION/STUDENT_INFO.md](DOCUMENTATION/STUDENT_INFO.md).

## Tech stack

- PHP 8.2 + Laravel 11
- Hand-written HTML5 + CSS3 in Blade templates, vanilla JavaScript (`fetch`) for AJAX
- MySQL 8

## Repository structure

```
VoltCart/
├── CODE/            Laravel application source code
├── DATABASE/        schema.sql, sample-database-data.sql, ERD
├── DATA/            sample product/category data (and CSV files if implemented)
├── DOCUMENTATION/   STUDENT_INFO.md, SETUP.md, TEST_CASES.xlsx, FINAL_REPORT.pdf, checkpoints
├── VIDEO/           DEMO_LINK.txt
└── README.md
```

## Where to find things

| I want to… | Open |
|---|---|
| Install and run the website | [DOCUMENTATION/SETUP.md](DOCUMENTATION/SETUP.md) |
| See the features, actors and business rules | [DOCUMENTATION/analysis/PHAN_TICH_NGHIEP_VU.md](DOCUMENTATION/analysis/PHAN_TICH_NGHIEP_VU.md) (Vietnamese) |
| See the business diagrams (use case, order flow, ERD…) | [DOCUMENTATION/analysis/diagrams/](DOCUMENTATION/analysis/diagrams/) |
| Understand what each code folder is for | [DOCUMENTATION/guides/CODE_MAP.md](DOCUMENTATION/guides/CODE_MAP.md) (Vietnamese) |
| Add a new page or feature | [DOCUMENTATION/guides/HOW_TO_ADD_FEATURE.md](DOCUMENTATION/guides/HOW_TO_ADD_FEATURE.md) (Vietnamese) |
| Know who is in the team and who does what | [DOCUMENTATION/STUDENT_INFO.md](DOCUMENTATION/STUDENT_INFO.md) |
| Read the checkpoint reports | [DOCUMENTATION/checkpoints/](DOCUMENTATION/checkpoints/) |

## Code organisation

The application is split into three areas. Controllers, views and routes use the same area name.

| Area | For | Controllers | Views | Routes |
|---|---|---|---|---|
| Shop | Guests and members: home, products, search, cart, checkout, chat | `app/Http/Controllers/Shop` | `resources/views/shop` | `routes/shop.php` |
| Account | Members: register, login, profile, order history | `app/Http/Controllers/Account` | `resources/views/account` | `routes/account.php` |
| Admin | Administrators: products, orders, customers, coupons, reports | `app/Http/Controllers/Admin` | `resources/views/admin` | `routes/admin.php` |

Business rules (pricing, stock, coupons, order status) are written once in `app/Services`.

## Workflow

- `main`: stable code, only updated through pull requests.
- `develop`: integration branch.
- `feature/<name>`: one branch per task (e.g. `feature/cart-ajax`), merged into `develop` via a pull request reviewed by another member.
- Each member commits their own work with their own GitHub account.
- Never commit `.env`, `vendor/` or `node_modules/`.
