# Auto Mercy application blueprint

## Phase overview

This directory is the implementation contract for Phase 2: information architecture and technical planning. It describes the intended website without creating routes, database objects, authentication, pages, Livewire components, or Filament resources.

The approved brand documents in [`../brand`](../brand/) remain the authority for identity, tone, colours, typography, business facts, contact details, car-stand names, and commercial wording. Runtime content ownership is defined in [application-architecture.md](application-architecture.md#content-ownership). If a later approved brief conflicts with these documents, record the decision before implementation.

## Architecture summary

Auto Mercy will be a conventional Laravel monolith:

- Blade renders public, crawlable pages on the server.
- Livewire progressively enhances inventory search and filtering.
- Tailwind CSS implements the approved design tokens and responsive UI.
- Filament supplies a private `/admin` panel.
- MySQL is the transactional source of truth.
- Laravel filesystem disks keep media independent of a storage vendor.
- GA4 measures consent-permitted interactions; Search Console monitors organic search.

Livewire and Filament are **proposed, compatible, and not installed**. Package resolution belongs to Phase 3.

## Document index

| Document | Implementation decisions covered |
|---|---|
| [Application architecture](application-architecture.md) | Runtime boundaries, stack ownership, compatibility, media, content sources |
| [Sitemap](sitemap.md) | Public hierarchy, navigation, page purposes, publication gates |
| [Routes](routes.md) | URL and route-name contract, access, canonical and conflict rules |
| [Customer journeys](customer-journeys.md) | Discovery, enquiry, visit, reservation, delivery, empty and Sold flows |
| [Data model](data-model.md) | Entities, fields, relationships, constraints, indexes and lifecycle |
| [Inventory experience](inventory-experience.md) | Filters, URL state, sorting, pagination, cards and car details |
| [Admin dashboard](admin-dashboard.md) | Filament resources, workflows, roles and audit rules |
| [SEO requirements](seo-requirements.md) | Metadata, indexation, structured data, sitemaps and Sold URLs |
| [Analytics events](analytics-events.md) | GA4/internal event contract, privacy and deduplication |
| [Non-functional requirements](non-functional-requirements.md) | Performance, security, accessibility, media, backup and operations |
| [Implementation roadmap](implementation-roadmap.md) | Fourteen gated implementation phases |

## Approved decisions

- Public brand: **Auto Mercy**; legal name: **Auto Mercy of God Nigeria Limited**.
- The two physical locations are the **Iju car stand** and **Ogunnisi Road car stand**.
- Inventory is foreign-used cars. The website is enquiry-led; the primary actions are WhatsApp, phone, directions, and visiting a car stand.
- The reservation deposit is **₦500,000**, non-refundable, and holds a car for **14 days**. Outstanding payment is due within that period. If it is not completed, the deposit is forfeited for administrative and vehicle-holding costs. Payment occurs outside the website.
- Nationwide delivery is available within Nigeria; the cost depends on destination and must be confirmed.
- Lifecycle values are `draft`, `available`, `reserved`, `sold`, and `archived`. “Featured” and “recently added” are presentation attributes, not statuses.
- `/cars` shows Available cars by default. Reserved cars can be viewed directly and through an explicit availability filter. Sold cars remain accessible initially but leave active inventory. Archived car URLs return 410 unless a highly relevant redirect is selected manually.
- Public pages are Blade-first. Livewire is restricted to interactions where it materially helps, principally URL-synchronised inventory controls.
- Initial authorization uses a role value on `users`, Laravel policies, and Filament panel/resource authorization. A granular permissions dependency is not justified for two roles.
- Important contact and commercial facts have one typed runtime source; secrets remain environment-only.

### Confirmed business facts for implementation

| Fact | Approved value |
|---|---|
| Public / legal name | Auto Mercy / Auto Mercy of God Nigeria Limited |
| Registration / history | CAC 7328497; operating since 2023 |
| Phone and WhatsApp display number | 08061731673; production international/deep-link format still requires confirmation |
| Email | automercyofgod19@gmail.com |
| Hours | Monday–Saturday, 8:00 AM–6:00 PM |
| Iju car stand | 9 Moshalashi Alao Street, Oyemukun Bus Stop, Iju Road, Lagos |
| Ogunnisi Road car stand | 6/8 Ogunnisi Road, Bamboo Plaza, adjacent Omole Phase 1, Lagos |
| Social references | Instagram and Threads `@automercy.ng`; Facebook `Akeem Balogun`; YouTube `AutomercyofGodnigeria`; canonical URLs require verification |
| Sales/delivery area | Lagos, with nationwide delivery within Nigeria; delivery cost depends on destination |

Import these approved values into the structured runtime sources during content population. Templates must not parse this table or duplicate it locally.

## Deferred decisions and prerequisites

| Decision | Gate before implementation |
|---|---|
| Production hostname and canonical host | Hosting/domain approval |
| Approved international phone/WhatsApp format and deep links | Business confirmation |
| Canonical social-profile and map URLs; car-stand coordinates | Verified URLs supplied |
| `public/logo.jpeg` production use | Provenance and production approval; transparent/header/favicon variants still absent |
| Production media provider and CDN | Hosting topology and budget |
| Image processing library and AVIF capability | Composer compatibility and representative media tests |
| Analytics consent mode, internal-event retention and privacy wording | Privacy/legal review |
| Sold-page retention after the 90-day review point | Search performance and business-value review |
| Reservation-expiry automation | Explicit operational approval; launch uses an admin alert and manual decision |
| Negotiable-price field | Business requirement |
| Testimonials | Genuine approved content and consent |
| Error-monitoring vendor; final RPO/RTO | Hosting/operations decision |

## Major constraints

- Do not invent inventory, testimonials, reviews, coordinates, delivery prices, staff claims, ratings, social URLs, or production contact formats.
- No public customer accounts, customer-conversation capture, payment workflow, or generic page builder is planned.
- Core filters must use dedicated columns, not a catch-all JSON field.
- The brand token system must not be replaced or silently altered.
- The current app is a Laravel starter: the only application route is `/`, the welcome page is generic, and no admin/auth UI exists.
- There is no Git metadata in this directory. Phase 2 validation therefore uses an explicit file inventory and content checks rather than a Git diff.

## Phase 2 boundary

Only the documents in this directory are Phase 2 deliverables. The next authorized phase may begin package setup and database/domain implementation after the deferred launch-critical inputs are assigned owners.
