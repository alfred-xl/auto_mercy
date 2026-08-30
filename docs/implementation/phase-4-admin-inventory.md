# Phase 4 Filament vehicle and inventory management

**Status:** Complete  
**Completed:** 2026-08-29  
**Application timezone:** `Africa/Lagos`

## Delivered administration experience

- Added a private, policy-backed Filament inventory area for cars, makes, models, body types, features, and the two approved car stands.
- Added a Super Administrator-only Administrators resource at `/admin/administrators`; no public registration or public website pages were introduced.
- Added dashboard widgets for lifecycle counts, inventory-quality gaps, reservation deadlines, and the ten most recently updated vehicles.
- Added searchable/sortable car inventory with status, make, stand, and soft-delete filters, plus accessible empty states.
- Added structured car create, edit, and view screens for identity, pricing, specifications, content, features, SEO, lifecycle facts, and gallery management.

## Lifecycle controls

Cars are always created as Draft. Ordinary form submissions cannot write status or lifecycle timestamps. Publish, reserve, return-to-available, mark-sold, archive, and archived-to-draft controls call the existing transactional `TransitionCarStatus` action and record the acting administrator in `updated_by`.

Publication checks require the complete vehicle data contract and a ready primary image belonging to the car with reviewed alternative text. Reservation UI displays the centrally configured ₦500,000 amount and defaults to the centrally configured 14-day period. Automatic reservation expiry remains deliberately excluded; the dashboard only reports overdue and approaching deadlines.

Duplication creates a new Draft with a new stock number and slug. It copies appropriate vehicle details and feature assignments, but never copies images, primary-image state, featured state, SEO fields, lifecycle timestamps, or status.

## Media workflow

- The authoritative gallery is `car_images`; `cars.primary_image_id` remains the sole primary-image source of truth.
- Upload accepts only genuine JPEG, PNG, and WebP files, capped at 15 MiB and 40 megapixels.
- Server-side inspection ignores the browser-provided extension and MIME claim. Accepted files are decoded and re-encoded through GD, receive UUID filenames, and are written to the configured `CAR_MEDIA_DISK` under a stock-specific directory.
- Original filenames are retained only as metadata. Width, height, MIME type, encoded size, order, processing state, and creator are persisted.
- Admins can edit alternative text/captions, set a primary image, and reorder using keyboard-accessible move controls.
- Deleting the primary image promotes the first remaining active image. If none remains, the primary pointer becomes null and publication is blocked until a replacement is selected.
- Soft-deleted cars retain their image records. Force-delete actions are not exposed.

## Authorization and administrator safety

- Active Super Administrators manage all Phase 4 resources.
- Active Inventory Managers manage cars, lifecycle actions, images, and features while reference resources remain view-only under the Phase 3 policies.
- Inventory Managers cannot access the Administrators resource, including by direct URL.
- Administrator creation and update use dedicated actions, hashed passwords, explicit role assignment, and lower-cased unique email addresses.
- Self-deactivation is blocked. The final active Super Administrator cannot be demoted or deactivated. Administrator deletion is not exposed.
- Reference-record deletion is available only to authorized users and only when the record has no attached cars; restrictive foreign keys remain the final integrity boundary.

## Configuration

Phase values live in `config/automercy.php`: `NGN`, a reservation amount of `500000`, a 14-day duration, `CAR_MEDIA_DISK` (default `public`), JPEG/PNG/WebP formats, and limits of 15 MiB and 40 megapixels.

No schema migration or persistent sample inventory was required. Existing Phase 3 tables, relations, enums, observers, policies, factories, and the two approved stand records were preserved.

## Verification evidence

- Every Phase 4 PHP file passed syntax validation.
- Admin routes expose the expected car, reference, stand, and administrator resources.
- Direct rendering was tested for car list/create/view/edit, all reference list/view/edit screens, and role boundaries.
- Domain tests cover image validation/re-encoding/storage, ordering, primary replacement, disguised uploads, safe duplication, administrator creation, self-deactivation, and final-Super-Administrator protection.
- The full Pest suite passes against isolated MySQL `auto_mercy_testing`.
- Laravel Pint and the production Vite/Tailwind build pass.

The local PHP CLI has no `pdo_sqlite`, so database verification uses isolated MySQL. The developer database `auto_mercy` was not refreshed, reseeded, or destructively modified.

## Manual browser handoff

Create an administrator through `php artisan app:create-admin`, then visit `/admin`. Recommended checks are mobile/desktop navigation, keyboard use of gallery controls and dialogs, draft creation followed by gallery/alt-text review, every permitted lifecycle transition, direct-URL role boundaries, and primary-image deletion behavior.
