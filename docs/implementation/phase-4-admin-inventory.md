# Staff vehicle operations admin

**Status:** Implemented

**Updated:** 2026-09-05

**Application timezone:** `Africa/Lagos`

## Staff navigation and dashboard

The Filament panel is the dealership's operational workspace. Vehicles, Leads and Financing are top-level destinations. Makes, Models, Body Types and Features are grouped under Catalogue. Company locations come from the fixed business configuration rather than a separate Car Stands resource. Administrators is visible and accessible only to active Super Administrators; policies also enforce this restriction for direct URLs.

Dashboard cards link to actionable queues for vehicle lifecycle states, incomplete listings, invalid primary images, new leads, overdue follow-ups, inspections in the next seven days, active financing work and individual financing stages. The recent-vehicles table links directly to edit screens.

## Vehicle workflow

Vehicles always begin as Draft. The create/edit experience is a non-skippable five-step wizard: Vehicle, Listing, Specifications, Photos and Review. Required fields are validated before the next step becomes accessible. Make changes clear the selected model, and only active models belonging to the selected make are offered. Stock numbers, slugs and SEO metadata are generated automatically and are not exposed as staff inputs.

The listing supports stock/make/model/year/trim search; status, make, model, company location, body type and readiness filters; primary thumbnails; lifecycle badges; and explicit readiness. Staff actions include view, edit, authenticated preview, duplicate as Draft, publish, reserve, return to Available, mark Sold, archive and restore an Archived vehicle to Draft. Permanent deletion is not exposed.

Status is never edited through the form. All changes use `TransitionCarStatus`, its allowed transition map and backend policy checks. Most specifications are optional. Publication requires identity, price, description, a valid configured company location and a ready primary image with alternative text. Returning to Draft or Available clears stale reservation, sale and archive timestamps as appropriate.

## Gallery and responsive images

Gallery uploads accept genuine JPEG, PNG and WebP files up to 15 MiB and 40 megapixels. GD decodes and re-encodes each upload, corrects supported JPEG EXIF orientation, removes source metadata, constrains oversized originals and creates thumbnail, card, medium and large WebP derivatives. AVIF derivatives are created only when the installed GD build provides `imageavif`.

Derivative metadata includes path, width, height, format key and encoded size. Public vehicle cards and detail pages use responsive `srcset`/`sizes`, prefer AVIF when available and otherwise serve WebP derivatives. The original is only a compatibility fallback for images uploaded before derivatives existed.

Staff can select multiple images directly in the Photos wizard step, see previews, add per-image alternative text/captions and choose a new primary photo before saving. The saved-gallery controls continue to provide processing state/dimensions/file size, replacement, primary selection, drag ordering and keyboard-friendly move buttons. A public vehicle's only valid primary image cannot be removed. Removal quarantines files before committing the database change and restores them if the transaction fails.

Media settings are centralized in `config/automercy.php`, including `CAR_MEDIA_DISK`, accepted MIME types, upload limits, maximum dimensions, variant widths and compression quality.

## Leads

Vehicle pages have a minimal validated, CSRF-protected and rate-limited enquiry form. Submissions create Website leads in the New stage. Leads store customer contact details, interested vehicle, message/source, owner, stage, follow-up and inspection times, notes and contact timestamps.

The stages are New, Contacted, Qualified, Inspection Scheduled, Negotiating, Won and Lost. Staff can assign, edit notes and dates, change stage, filter by stage/owner/vehicle/follow-up state, and open Call, WhatsApp or Email hand-offs. WhatsApp conversations are never stored or read.

## Financing

Financing requests link to a lead and optionally a vehicle. They store the vehicle price, requested amount, deposit, provider, assigned staff, follow-up date, notes and status. Stages are New Request, Documents Pending, Under Review, Approved, Declined and Completed. Tables and dashboard cards provide direct stage and overdue-work queues; no lending decision or payment automation is included.

## Roles and daily use

Active Inventory Managers can manage vehicles, galleries, leads, financing and permitted catalogue data. Active Super Administrators have those permissions and can also manage administrator accounts. Custom mutating actions authorize on the backend before changing records.

A normal daily flow is:

1. Open dashboard attention cards and handle overdue leads or financing work.
2. Create a vehicle Draft and fill what is currently known.
3. Upload and order photos, write accurate alt text and choose a primary image.
4. Resolve every item in Publication readiness, preview, then Publish.
5. Use only the lifecycle row/header actions as the vehicle is reserved, sold or archived.

## Deployment and deferred work

Run the new migrations before using the revised workflow. The location migration backfills existing assignments into fixed company-location keys. Ensure the configured media disk is publicly linked and confirm the PHP GD extension supports WebP. AVIF is optional and detected at runtime.

Automated loan decisions, online payments, WhatsApp conversation storage, automatic reservation expiry, background media queues and Cloudinary video upload remain deliberately deferred. Existing images are not backfilled automatically; re-upload or replace a legacy image to generate its responsive variants.

Laravel Pint and the production asset build are part of this handoff. Automated tests were intentionally not run at the user's request.
