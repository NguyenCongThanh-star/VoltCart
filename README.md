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

## Getting started

Installation and run instructions: [DOCUMENTATION/SETUP.md](DOCUMENTATION/SETUP.md).

## Workflow

- `main`: stable code, only updated through pull requests.
- `develop`: integration branch.
- `feature/<name>`: one branch per task (e.g. `feature/cart-ajax`), merged into `develop` via a pull request reviewed by another member.
