# Administration dashboard

Filament 5 is the planned private panel at `/admin`; it is compatible but not installed. The panel is a delivery mechanism for Laravel policies, validation, and domain actions—not the owner of business rules.

## Navigation and resource plan

| Group | Resources/pages | Key responsibilities |
|---|---|---|
| Dashboard | Overview | Counts, recent operational items, click/view trends with honest labels |
| Inventory | Cars, media manager, reservation-overdue queue | Create/edit/preview/publish, status actions, images, stock search |
| Catalog | Makes, models, body types, features | Curated reusable values and active state; safe referenced-value handling |
| Content | Fixed pages, FAQs, buying guides | Structured content, drafts, preview, publication and SEO fields |
| Car Stands | Iju, Ogunnisi Road | Exact address/hours/content/media and assigned inventory |
| Analytics | Vehicle interactions | Aggregate first-party views/clicks only; CSV export deferred unless approved |
| Settings | Contact, hours, social, reservation, footer | Validated typed singleton; no environment secrets |
| Administration | Users, read-only activity/status logs | Super Administrator only except each user’s own account controls |

No general-purpose page builder is planned. Core content uses fixed keys and structured fields so editors cannot create accidental routes, inaccessible layouts, or duplicate contact facts.

## Dashboard widgets

| Widget | Source and definition |
|---|---|
| Available / Reserved / Sold / Draft cars | Current lifecycle status counts; Archived separate or filterable |
| Recently added | Most recently created Draft/Available records, labelled by status |
| Recently reserved | Latest Reserved transitions from status history |
| Reservation deadlines | Reserved cars ordered by expiry; overdue highlighted for human review |
| Most viewed cars | First-party `view_car` counts for selected period, only if internal tracking is approved |
| Most-clicked cars | Sum or breakout of stored CTA clicks; label as clicks, never customer outcomes |
| WhatsApp / Phone / Directions clicks | Allowlisted internal event aggregates by period, car and CTA position |

Widgets state date range, data source, last refresh, and empty state. GA4 aggregates are not silently mixed with first-party data. No widget calls an interaction a confirmed enquiry or sale.

## Car management workflow

### Create and edit

- A new record starts Draft, receives a unique immutable stock number, and has no public slug until ready.
- Form sections cover identity/classification, price/mileage, specifications, description/features, car stand, media/video, featured state, SEO, and lifecycle readout.
- Make controls the available model options; server validation proves the relationship.
- Save Draft permits incomplete records but validates each supplied value.
- Preview uses a signed, short-lived, authorized route that cannot be indexed or shared as public inventory.
- Publication calls one domain action and reports every missing requirement from the publication invariant.
- Unpublish is an explicit reason-required transition to Draft; it is not deletion.

### Lifecycle actions

| Action | Preconditions / result | Role |
|---|---|---|
| Publish | Complete Draft + processed primary image → Available; sets slug/`published_at` | Inventory Manager if policy approves; Super Administrator |
| Unpublish | Available → Draft; removes public response and records reason | Both, subject to policy |
| Reserve | Available + confirmation that external deposit was received → Reserved; sets `reserved_at` and 14-day expiry | Both |
| Release reservation | Reserved → Available after an approved cancellation/expiry decision; clears current reservation fields but keeps history | Both; reason required |
| Mark Sold | Available or Reserved → Sold; sets `sold_at`, removes hold actions | Both |
| Archive | Draft/Available/Reserved/Sold → Archived under allowed transition; sets `archived_at` | Super Administrator; Inventory Manager only if explicitly granted for inventory policy |
| Restore | Archived → Draft only | Super Administrator |
| Feature/unfeature | Published Available record only for public placement | Both |

The deadline job is initially a read-only notifier. It must not change status or apply a financial consequence. Status actions are idempotent, transactional, authorized server-side, and write `car_status_histories` plus activity logs.

### Duplicate listing

Duplication copies safe specifications, description, stand, and features as a starting point. It creates a Draft and does **not** copy stock number, slug, publication/lifecycle timestamps, status, featured state, SEO/canonical override, analytics, history, or images. The administrator confirms all vehicle-specific facts and adds fresh media before publication.

### Lists, search, and bulk actions

Search by stock number, year/make/model/trim, and exact slug. Filters cover status, make, model, body type, car stand, featured state, publication date, and missing primary/processing error. Default columns show primary thumbnail, stock, title, price, status, stand, published/updated timestamps, and updater.

Bulk mutation is limited to low-risk operations such as feature assignment only after policy review. Do not bulk-publish, bulk-change lifecycle, or force-delete because each record needs validation and audit context.

## Image management

- Multi-upload enters private staging with progress and per-file validation feedback.
- Display order is keyboard-operable as well as pointer-operable; save reorder transactionally.
- Primary selection updates only `cars.primary_image_id` after the image is processed.
- Alt text is generated as an editable suggestion from visible car facts/view order; an administrator reviews it.
- Replacement keeps the old file until the new variants succeed and the pointer update commits.
- The primary cannot be deleted without replacement or unpublishing. Media deletion requires policy checks and only touches paths owned by that image record.
- Processing failures do not publish partial files. Show retry/removal actions and preserve an audit event.
- Upload constraints and variants follow [non-functional-requirements.md](non-functional-requirements.md#media).

## Content management

| Content | Interface and approval |
|---|---|
| Car stands | Structured location record; Super Administrator approves address/map/hour changes |
| FAQs | Question, answer, category, order, active state; only genuine approved answers |
| Buying guides | Draft/publish workflow, sanitized editor, author/dates, cover/alt, metadata |
| Policies | Fixed Reservation/Privacy/Terms keys; Super Administrator and business approval; version note in audit |
| About, How to Buy, Delivery, Contact intro | Fixed-key structured page forms; no arbitrary layout blocks |
| Contact/social/hours/footer/reservation values | Typed singleton settings with confirmation for high-impact changes |
| Homepage | Approved heading/copy/settings; featured cars are derived from published car flags |

Settings/content cache is invalidated after a successful commit. Environment values, credentials, analytics secrets, application debug settings, and storage credentials have no editable admin field.

## Roles and authorization

Store one controlled role on `users`; use Laravel policies/gates and Filament panel/resource checks. Filament automatically consults model policies for standard resource actions, but every custom page/action and underlying domain action needs explicit authorization. A dedicated permission package is not justified until permissions must be administrator-configurable or roles materially expand.

| Capability | Super Administrator | Inventory Manager |
|---|:---:|:---:|
| Access panel / own profile | Yes | Yes |
| Manage administrator accounts and roles | Yes | No |
| Create/edit cars and images/features | Yes | Yes |
| Preview/publish/unpublish | Yes | Yes, if publication policy remains approved |
| Reserve/release/mark Sold | Yes | Yes |
| Archive/restore/force purge | Yes by explicit action | No by default |
| Manage makes/models/body types | Yes | Yes for inventory needs; destructive changes restricted |
| Manage car stands | Yes | No |
| Manage guides/FAQs/About/delivery | Yes | No by default |
| Change legal policies, core contact/reservation settings | Yes with approval | No |
| View vehicle interaction aggregates | Yes | Yes |
| Access activity/status logs | Yes, read-only | Status history for assigned inventory, read-only |
| Delete/alter logs or sensitive application configuration | No normal UI | No |

Panel access also requires `is_active`. Authorization is enforced on server requests, not only by hiding navigation.

## Soft deletion and audit

Cars, guides, and managed long-form content use soft deletion where recovery is useful. Taxonomies and users are deactivated when referenced. Force deletion is a rare Super Administrator workflow with dependency preview, re-authentication, typed confirmation, reason, and backup/recovery awareness; media purge happens after commit.

Audit high-impact operations: authentication/admin account changes; settings/policy changes; publication; every lifecycle action; price/stand/stock/slug change; media upload/reorder/primary/replacement/deletion; restore/purge; and denied sensitive actions where useful. Store sanitized diffs, actor, subject, action, reason, timestamp, and correlation ID. Logs are append-only and excluded from normal deletion.

## Admin usability and security acceptance

- Every form has explicit labels, error summaries, inline messages, keyboard order, and unsaved-change warning where appropriate.
- Destructive/status actions use focused confirmation that names the car and resulting public state.
- Global search never exposes records the user cannot view.
- Login is throttled; sessions use production-secure cookies/HTTPS; no public registration.
- MFA is strongly recommended and should be required for Super Administrators once the selected Filament-compatible implementation is verified.
- Admin tests cover role denial as well as successful actions, including direct URLs and custom actions.
