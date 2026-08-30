# Non-functional requirements

These are launch acceptance requirements unless marked provisional. Test representative mobile devices, real car imagery, MySQL data, production-like caching, and constrained networks—not only the starter dataset.

## Performance

### Targets

At the 75th percentile, separately for mobile and desktop field data:

- Largest Contentful Paint ≤ 2.5 seconds.
- Interaction to Next Paint ≤ 200 ms.
- Cumulative Layout Shift ≤ 0.1.

These match current Core Web Vitals “good” thresholds ([web.dev Web Vitals](https://web.dev/articles/vitals)). Also target a useful server response under 800 ms at the origin for cached public reads, subject to hosting baselines, and keep core pages functional on slow mobile networks.

### Delivery requirements

- Server-render all public content, navigation, inventory links, metadata, and JSON-LD. Keep JavaScript minimal and route-specific.
- Load only the LCP image eagerly/high-priority; lazy-load below-fold media. Set intrinsic dimensions/aspect ratios to prevent shifts.
- Use responsive variants and modern formats with a proven fallback. Do not autoplay video.
- Self-host the approved fonts where licensing permits, subset/preload only critical faces, use `font-display: swap`, and avoid loading unused weights.
- Use no animation that delays interaction; honour reduced motion and animate only compositor-friendly properties.
- Eager-load card/detail relationships and select only required columns. Add query-count tests and representative MySQL `EXPLAIN` checks; prevent N+1 queries.
- Paginate inventory (initially 12); do not load all cars/images.
- Cache stable taxonomies, settings, public page content, and qualified landing queries with explicit post-commit invalidation. Never serve stale availability after a lifecycle action; use short TTL/versioned keys where necessary.
- Production deploy runs Laravel configuration, route, and view caches, plus optimized Composer autoloading and built Vite assets. Warm critical caches after deploy.
- CDN/object caching uses immutable media paths and appropriate cache headers; HTML availability remains revalidatable.
- Establish bundle, image, query-count, and Lighthouse budgets during implementation and fail CI on material regression after baselines are approved.

## Security

### Authentication and authorization

- No public registration. Administrator accounts are created by an authorized Super Administrator or controlled deployment process.
- Require strong passwords (provisional minimum 12 characters), breached-password screening if a compatible service/process is approved, secure reset flow, generic login errors, and login/admin rate limits.
- Require HTTPS in production; cookies are Secure, HttpOnly, and appropriate SameSite; rotate the application key only through an approved recovery plan.
- MFA is strongly recommended and should be mandatory for Super Administrators after verifying the chosen Filament-compatible implementation.
- Enforce Laravel policies on resources, custom actions, signed preview, internal-event writes, and media changes. UI hiding is not authorization.
- Re-authentication is required for administrator/role changes, high-impact settings, force purge, and MFA changes.

### Application and data

- Use CSRF protection for state-changing web requests, strict request validation, output escaping, and HTML sanitization for approved rich-text fields.
- Avoid mass-assignment of role, status, lifecycle timestamps, stock number, ownership, disk/path, or audit fields. Domain actions set them.
- Do not log credentials, tokens, environment values, storage keys, database details, customer conversations, or full request bodies containing sensitive fields.
- Production disables debug output and maps errors to helpful generic pages with correlation IDs.
- Use least-privilege database/storage credentials, secrets only in environment/secret management, dependency/security scanning, supported runtimes, and a documented patch process.
- Set reviewed security headers: HSTS after HTTPS validation, CSP/reporting plan, frame restrictions, referrer policy, content-type sniffing protection, and a minimal Permissions Policy. External WhatsApp/map/video origins must be explicit.
- Rate-limit login, password reset, preview generation, analytics writes, and expensive inventory endpoints. Protect scheduler/queue dashboards if introduced.

## Accessibility

Target WCAG 2.2 AA across public and admin workflows.

- Use semantic landmarks, one meaningful H1, logical heading levels, real links/buttons, lists/tables where appropriate, and a skip link.
- All actions work by keyboard with visible high-contrast focus and predictable order. No keyboard trap except a correctly implemented modal/drawer; Escape closes and focus returns to the trigger.
- Mobile menu/filter drawer/gallery dialogs have accessible names, state, focus containment, scroll locking, and background inertness.
- Every control has a persistent label/instruction. Errors have an accessible summary plus field association; values are not conveyed by placeholder alone.
- Status is visible text/icon, never colour alone. Brand combinations must pass AA contrast in actual component states.
- Images have purposeful alt text; decorative images use empty alt. Gallery controls announce current image/count without repeatedly reading a long description.
- Filter results set `aria-busy`, preserve focus, and announce result counts through a polite live region. Loading does not erase context.
- Pagination uses links, an accessible navigation label, descriptive Previous/Next labels, and `aria-current`.
- Honour `prefers-reduced-motion`; no flashing, forced autoplay, or essential hover-only content.
- Target at least 44×44 CSS-pixel touch actions with adequate spacing. Support 200% browser zoom and text reflow without horizontal page scrolling at 320 CSS pixels, except genuinely two-dimensional content.
- Sticky mobile actions respect safe areas, do not cover content/browser controls/errors, and remain reachable above the on-screen keyboard.
- Validate with automated checks plus keyboard, screen reader, zoom/reflow, contrast, and mobile-device manual testing.

## Mobile behaviour

Design mobile-first for intermittent connectivity. Header/menu/filter controls retain stable dimensions. Detail pages prioritize status, price, image, Call, and WhatsApp. A bottom action bar contains at most two actions and reserves matching content space. Drawers use explicit Apply/Cancel; unsaved staged changes are not silently committed. External-app failures leave visible contact information. Landscape and small-height screens must still expose close/submit controls.

## Browser support

Support the current and previous major versions of Chrome, Edge, Firefox, and Safari; current iOS Safari and Android Chrome; and common embedded browsers used by WhatsApp/social links on a best-effort basis. Progressive HTML forms, anchors, images, and pagination remain usable without optional JavaScript. Do not add polyfills until analytics/support testing identifies a required capability. Document any intentionally unsupported feature and provide a fallback.

## Media

### Input policy

- Initial allowed decoded inputs: JPEG, PNG, and WebP. Permit AVIF upload only after the selected decoder is verified in every environment. Reject SVG and non-image/polyglot content.
- Maximum upload: 15 MiB per file and 40 megapixels after decode (provisional; validate memory under concurrency). Recommend at least 1200×900 for a primary image; lower-resolution images may stay Draft with an explicit quality warning.
- Verify MIME from content, successful decode, dimensions, file size, and extension consistency. Generate random internal paths; never execute uploads.
- Correct EXIF orientation, strip EXIF/GPS and unnecessary metadata, re-encode, and scan/validate before public publication.

### Output policy

- Preserve a sanitized private original when operationally affordable.
- Generate deterministic widths near 320, 640, 960, 1280, and 1920 without upscaling; JPEG fallback plus WebP, and AVIF only after encoder/quality/support testing.
- Record width, height, bytes, MIME, and derivative manifest. Use intrinsic markup dimensions and `srcset`/`sizes`.
- Thumbnails use the same safe pipeline. An approved neutral fallback covers missing/failed media and never impersonates a car.
- Paths are immutable/versioned. Replacement publishes new objects, updates the database transactionally, then retires old objects after a recovery window.

### Storage and deletion

Use Laravel filesystem disks and persist disk/path only. Local public storage is acceptable for development or one durable server; production object storage is preferred for ephemeral/multi-node hosting. R2/S3 credentials stay in environment configuration and provider migration is tested with URL/caching behaviour.

Only an authorized action may delete files, and only exact paths owned by the record. Soft-deleted cars retain media until approved purge. Database rollback and filesystem cleanup use an outbox/after-commit/reconciliation approach so partial failure is recoverable. Back up originals and metadata; derivatives must be reproducible.

## Database, queues, and caching

- MySQL strict mode, `utf8mb4`, foreign keys, unique constraints, transactions, and documented indexes are required.
- Persist UTC timestamps; present reservation deadlines in `Africa/Lagos`. Test boundary time and scheduler timezone.
- Migrations must be reversible where safe, tested on a production-like copy, and never mix irreversible data loss into an unattended deploy.
- Database-backed queue/cache/session defaults require appropriate tables, workers, pruning, retry/failure monitoring, and capacity. Deployment may select Redis later without changing domain logic.
- Queue image processing and other slow work after commit. Jobs are idempotent, bounded, uniquely keyed where appropriate, have timeouts/retries/backoff, and never publish a car before required media succeeds.

## Backups and recovery

Provisional launch objectives: RPO 24 hours and RTO 4 hours, to be approved against hosting cost. Take encrypted automated off-site database backups daily with at least 30 days retention; protect media originals through object versioning/snapshots or equivalent. Keep environment/secrets recovery material separately secured. Test a full database + media + configuration restore quarterly and before major infrastructure changes, recording duration and gaps. Soft deletion and storage versioning are not substitutes for backups.

Deployment needs a rollback path that covers code, migrations, built assets, cache, and worker compatibility. Never claim a backup is valid without restore evidence.

## Logging, monitoring, and error handling

- Emit structured logs with timestamp, environment, severity, correlation/request ID, route name, safe actor ID for admin actions, exception class, and sanitized context.
- Centralize production logs with access control, retention, alerts, and redaction. The provider and retention period are deferred.
- Monitor availability, 5xx rate, latency, queue backlog/failures, scheduler heartbeat, database connections/slow queries, storage/image failures, backup completion/restore tests, and security/auth anomalies.
- Use user-friendly 404, 410, 429, 500, and maintenance responses with core navigation and a correlation ID where useful. Never expose stack traces or internal paths.
- Public analytics failure never blocks navigation/contact. Admin mutations show a clear failure and do not imply success until the transaction commits.
- Define error-monitoring ownership, severity, response times, and escalation before production launch.

## Release gates

No launch with an authorization bypass, unsafe upload, unverified restore, inaccurate reservation wording, contradictory car availability, missing primary-media protection, analytics personal-data leak, broken keyboard journey, or unresolved canonical/indexation conflict. See phase-specific verification in [implementation-roadmap.md](implementation-roadmap.md).
