# Pawan Traders – Billing System

Simple PHP + MySQL billing & inventory system (GST-free). Originally a single
`index.php` file, now split into logical files for easier maintenance and
GitHub hosting.

## Project structure

```
config.php                DB credentials + shop info (edit before deploy)
db.php                    DB connection + auto schema creation + admin seed
index.php                 App bootstrap / router
includes/
  functions.php           e(), money(), go(), upiQrUrl() helpers
  auth.php                Login gate + logout handling
  login_view.php          Login page markup
  header.php               Shared top nav + sidebar + <head>
  footer.php               Closing HTML tags
actions/
  products.php             Add/update/delete product handlers
  customers.php             Add/update/delete customer handlers
  billing.php                Create-bill handler
pages/
  dashboard.php             Dashboard cards + recent bills
  customers.php              Customer search / list / form
  products.php                Product list / form
  billing.php                   New billing screen + JS
  sales.php                      Sales history table
  print_bill.php                 Printable invoice
  404.php                          Page-not-found fallback
```

## Setup

1. Create a MySQL server (tables are auto-created on first run).
2. Edit `config.php` with your DB host/user/pass and shop details.
3. Point your web server (Apache/Nginx + PHP) at this folder, open `index.php`.
4. Default login: `admin` / `admin123` — change this after first login.

## Before pushing to GitHub

- Don't commit real DB passwords or your real UPI ID in `config.php`.
  Either keep placeholder values in the repo and set real ones only on the
  server, or switch `config.php` to read from environment variables.
- Consider adding a `config.sample.php` with placeholder values and gitignore
  the real `config.php`.
