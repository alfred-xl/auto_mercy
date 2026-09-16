# Auto Mercy application architecture

This directory documents the implemented Laravel application and its remaining production gates. Runtime code and tests take precedence where an older planning document has not yet been reconciled.

## Current architecture

- Laravel and Blade render public crawlable pages on the server.
- Tailwind implements the established responsive public design.
- Filament provides the authenticated administration panel and the single vehicle-management workflow.
- MySQL is the transactional source of truth.
- Laravel storage and the existing WebP variants provide vehicle media.
- A configurable, consent-aware analytics adapter can dispatch approved non-PII events to GA4.

## Business and inventory model

Auto Mercy is one dealership and one legal organization, Auto Mercy of God Nigeria Limited. It has two physical locations: Iju Road and Bamboo Plaza on Ogunnisi Road. Both use the same telephone, WhatsApp, email, opening hours, organization identity, and shared inventory.

There is no branch inventory, vehicle-to-location assignment, location filter, duplicated listing, or required location field in the admin workflow. Every vehicle has one shared record and one public URL. Public copy asks customers to contact the team to confirm where a vehicle can be viewed before they visit; it does not claim that each vehicle is at both locations.

Inventory supports distinct listing categories: brand-new, foreign-used, and pre-order. These categories are separate from availability states: `draft`, `available`, `reserved`, `sold`, and `archived`. Available and Reserved vehicles are in active inventory. Retained Sold pages remain factual and enquiry-led but are excluded from active inventory. Draft is private, Archived is 410, and staff preview is authenticated/noindex.

## Confirmed runtime business facts

| Fact | Runtime value |
|---|---|
| Public / legal name | Auto Mercy / Auto Mercy of God Nigeria Limited |
| Registration / history | CAC 7328497; operating since 2023 |
| Phone / WhatsApp | 08061731673 / +2348061731673 |
| Email | automercyofgod19@gmail.com |
| Hours | Monday-Saturday, 8:00 AM-6:00 PM |
| Iju Road | 9 Moshalashi Alao Street, Oyemukun Bus Stop, Iju Road, Lagos |
| Bamboo Plaza | 6/8 Ogunnisi Road, Bamboo Plaza, adjacent Omole Phase 1, Lagos |

These values live in `config/automercy.php`; templates and structured data read that shared source. Configured map search links can be replaced with verified direct map URLs through the two map environment variables.

## Documentation index

| Document | Subject |
|---|---|
| [Application architecture](application-architecture.md) | Runtime boundaries and content ownership |
| [Sitemap](sitemap.md) | Public hierarchy and page purposes |
| [Routes](routes.md) | URL and route-name contract |
| [Customer journeys](customer-journeys.md) | Discovery, enquiry, visit, reservation and Sold flows |
| [Data model](data-model.md) | Entities, fields and lifecycle |
| [Inventory experience](inventory-experience.md) | Filters, pagination, cards and detail pages |
| [Admin dashboard](admin-dashboard.md) | Filament resources and workflows |
| [SEO implementation](seo-requirements.md) | Metadata, indexation, schema, landings and sitemap |
| [Analytics implementation](analytics-events.md) | Events, privacy, consent and activation |
| [Non-functional requirements](non-functional-requirements.md) | Performance, security, accessibility and operations |

## Remaining production gates

- Approved production hostname and HTTPS origin for `APP_URL` and `SEO_BASE_URL`.
- Verified proxy addresses/CIDRs, if applicable, for `TRUSTED_PROXIES`.
- Final verification of current addresses, hours, map destinations, contact details and social profile URLs.
- Review of real inventory and copy before enabling any curated SEO landing environment flag.
- Privacy approval, consent integration and a real analytics measurement ID before external dispatch is enabled.
- Search Console verification, sitemap submission, Google Business Profile maintenance, production structured-data checks, and live mobile/Core Web Vitals measurement.
- Genuine, approved testimonial content before testimonials return to public rendering.

Do not invent inventory, testimonials, reviews, coordinates, financing terms, warranties, delivery prices, staff claims, ratings, or production credentials. Do not introduce separate branch management or inventory as an indirect consequence of local SEO work.
