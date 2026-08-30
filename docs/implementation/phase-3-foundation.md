# Phase 3 application foundation and core vehicle domain

**Status:** Complete  
**Completed:** 2026-08-29  
**Application timezone:** `Africa/Lagos`

## Delivered foundation

- Installed and verified Livewire `4.4.2` and Filament `5.7.6` on Laravel `13.29.0` / PHP `8.4.22`.
- Registered one private Filament panel at `/admin`, with panel ID `admin`, Auto Mercy branding, the approved deep-red primary colour, login, and no public registration route.
- Added explicit panel admission through `FilamentUser`: only active `super_admin` and `inventory_manager` users can enter.
- Added an interactive `php artisan app:create-admin` command. It validates a unique email and a confirmed 12-character mixed-case, numeric, symbol-bearing password, permits only approved roles, activates the account, and never logs or seeds credentials.
- Added `role`, `is_active`, and `last_login_at` to the existing users table. Successful authentication updates the last-login timestamp.

## Vehicle domain

The phase adds string-backed enums for user roles, car status, transmission, fuel, drivetrain, vehicle condition, and mileage unit. It adds migrations, typed models, relationships, scopes, factories, and indexes for:

- `car_stands`
- `makes`
- `car_models`
- `body_types`
- `features`
- `cars`
- `car_images`
- `car_feature`

Cars and images use soft deletion. Reference records use restrictive foreign keys; final car/image and pivot cleanup uses the documented cascade/null behavior. Money is stored as integer `price_amount` in the declared three-letter currency rather than floating point.

## Identity and integrity rules

- A persisted car receives `AM-0001`-style stock identity from its committed database primary key. This is concurrency-safe, unique, stable, and does not use `MAX(id) + 1`.
- The initial readable slug uses year, make, model, optional trim, and stock number. It is generated after stock identity and remains stable on ordinary edits.
- A saving observer rejects make/model combinations where the model belongs to another make.
- A car must be created as Draft. Direct lifecycle-field mutation is rejected; callers must use the row-locking transition action.
- Publication requires the documented inventory fields plus a primary image that belongs to the same car.
- Supported lifecycle transitions are Draft→Available/Archived, Available→Draft/Reserved/Sold/Archived, Reserved→Available/Sold/Archived, Sold→Archived, and Archived→Draft. Publication, reservation, sale, and archive timestamps are maintained by the action. Reservation expiry is validated and defaults to 14 days when not explicitly supplied.

## Authorization baseline

- Active Super Administrators receive full policy access for the Phase 3 domains.
- Active Inventory Managers can view/create/update cars, perform publish/status/archive actions, manage car images and unattached features, and view reference data.
- Inventory Managers cannot manage users or roles, delete cars permanently, or mutate makes, models, body types, or car stands.
- Inactive and roleless users are denied both panel and policy access.

No Filament resources or custom widgets are included in this phase, so policy enforcement is ready before those direct-resource and custom-action entry points are added.

## Approved seed data

`DatabaseSeeder` calls only the idempotent `CarStandSeeder`. No user or sample vehicle is seeded.

| Stand | Slug | Address | Contact | Hours |
|---|---|---|---|---|
| Iju Car Stand | `iju` | 12/14 Iju Road, Opposite Mobil Petrol Station, Lagos, Nigeria | 08061731673 | Monday–Saturday, 08:00–18:00 |
| Ogunnisi Road Car Stand | `ogunnisi-road` | 17 Ogunnisi Road, Opposite First Bank, Ogba, Lagos, Nigeria | 08061731673 | Monday–Saturday, 08:00–18:00 |

Both records use Lagos city/state, the same number for phone and WhatsApp, and null map URL/coordinates. Sunday is represented as closed.

## Resolved document variations

- The current Phase 3 brief names the privileged role value `super_admin`; it takes precedence over the earlier architecture value `super_administrator`. The user-facing label remains “Super Administrator.”
- Image primacy follows the final architecture decision: `cars.primary_image_id` is the sole source of truth. There is no competing `car_images.is_primary` flag.
- Final architecture field names are used where the brief gave examples: `price_amount`, `display_order`, `original_filename`, and `file_size_bytes`.
- Status/audit history tables and activity log persistence are intentionally deferred because the Phase 3 entity boundary explicitly excluded them. The lifecycle action is designed as the single future audit integration point.

## Verification evidence

- Composer manifest validation: passed.
- Composer locked dependency audit: passed, no known vulnerability advisories.
- Production Vite/Tailwind build: passed.
- Pint formatting: passed.
- Full Pest suite: 25 tests passed, 78 assertions.
- Local MySQL forward migration: all 13 migrations ran successfully.
- Isolated MySQL fresh migration, seed, full-batch rollback, and clean re-application: passed.
- Seeder executed twice: exactly two approved stands remained.
- Admin route inspection: only `/admin`, `/admin/login`, and `/admin/logout` were registered for the panel.
- Filament access tests cover guest redirect, both active roles, inactive users, and roleless users.

The local PHP CLI does not provide `pdo_sqlite`, so verification used an isolated `auto_mercy_testing` MySQL database while retaining the portable in-memory SQLite configuration in `phpunit.xml` for environments where the extension is available.

## Operations and rollback

Create the first administrator interactively:

```bash
php artisan app:create-admin
```

Apply the schema and approved reference data:

```bash
php artisan migrate
php artisan db:seed
```

For rollback, use normal Laravel batch rollback only after backing up any environment that contains data. The migration order removes the circular primary-image foreign key before dropping image and car tables. Package rollback requires reverting `composer.json`, `composer.lock`, the registered panel provider, and published Filament assets together.

## Deferred work

Public pages, Filament resources, custom dashboard widgets, image upload/processing UI, activity and status history tables, content management, analytics, real administrator creation, and real inventory/media remain deferred to their planned phases.
