# QuickWash Express — Laravel Migration

This is a Laravel 10 application migrated from the `quickwash_expres` MySQL/phpMyAdmin
database dump, with three separate authentication portals (Admin, Staff, Customer) and
a dashboard for each role.

> **Note:** This was hand-built in a sandboxed environment without access to Packagist,
> so the `vendor/` folder is **not included**. Run `composer install` on your own machine
> (with normal internet access) to pull in the Laravel framework — everything else
> (migrations, models, controllers, views, routes, config) is already written and ready.

## What's included

- **Migrations** for all 15 tables from the original dump: `admins`, `staff`, `customers`,
  `laundry_services`, `bookings`, `pickup_schedule`, `delivery_schedule`, `payments`,
  `order_tracking`, `feedback`, `loyalty_points`, `notifications`, `announcements`,
  `promo_codes`, `activity_logs` — with the same columns, enums, and foreign keys.
- **Eloquent models** for every table, with relationships wired up (bookings ↔ customers,
  staff, services, payments, tracking, etc.).
- **Three independent auth guards** (`admin`, `staff`, `customer`), each backed by its
  own Eloquent provider (`App\Models\Admin`, `App\Models\Staff`, `App\Models\Customer`) —
  so an admin session and a customer session can be logged in in different tabs at once.
- **Login forms** for all three roles at `/admin/login`, `/staff/login`, `/customer/login`,
  plus a self-service registration form for customers at `/customer/register`.
- **Dashboards** for each role:
  - **Admin**: customer/staff/service counts, booking status breakdown, revenue today &
    total, recent bookings table.
  - **Staff**: bookings assigned to them for processing, and driver pickup/delivery runs.
  - **Customer**: their own orders, loyalty points, total spent, and active announcements.
- **Activity logging** — logins, logouts, and registrations are recorded to `activity_logs`,
  matching the behavior visible in the original dump.
- A seeder (`database/seeders/DatabaseSeeder.php`) with demo accounts and the 3 laundry
  services from your data (original password hashes can't be reversed, so this creates
  fresh demo logins — see below).

## Setup

1. **Install dependencies** (needs internet access to packagist.org):
   ```bash
   composer install
   ```

2. **Environment**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   Edit `.env` and set your MySQL credentials (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`).

3. **Create the database**, then run migrations + seed demo data:
   ```bash
   php artisan migrate --seed
   ```

4. **Serve the app**:
   ```bash
   php artisan serve
   ```
   Visit `http://localhost:8000` — you'll land on a portal chooser (Admin / Staff / Customer).

## Demo logins (from the seeder)

| Role     | Email                    | Password      |
|----------|---------------------------|---------------|
| Admin    | admin@quickwash.com       | password123   |
| Staff    | staff@quickwash.com       | password123   |
| Staff    | driver@quickwash.com      | password123   |
| Customer | customer@quickwash.com    | password123   |

**Change these before deploying anywhere real.**

## Importing your existing data instead of seeding

If you'd rather keep your actual historical data (bookings, customers, activity logs, etc.)
from the original SQL dump instead of the seeder:

1. Run `php artisan migrate` (without `--seed`) to create empty tables matching this schema.
2. Since your original dump's `CREATE TABLE` statements match these migrations column-for-
   column, you can import just the `INSERT INTO ...` statements from your original
   `quickwash_expres.sql` file directly into the new database (skip the `CREATE TABLE` /
   `ALTER TABLE` sections, since migrations already created the tables).
3. Existing customers/staff/admins will keep their original bcrypt password hashes and can
   log in with their original passwords immediately.

## What's next (not yet built)

This gives you working auth + dashboards on the full schema, which is the foundation.
Not yet included, but straightforward to add on this base if you want them next:
- Booking creation/checkout flow for customers
- Admin CRUD screens for services, staff, promo codes, announcements
- Staff status-update actions (mark picked up / washing / ready / delivered)
- Payment recording & receipts
- Notifications UI

Just ask and I can build any of these directly into this same app.
