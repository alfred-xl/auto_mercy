# Route plan

This is a future route contract, not an implemented route list. All public pages use `GET`/`HEAD`. Controllers/components named below are conceptual responsibilities; final class names may follow Laravel conventions without altering URLs or behaviour.

## Route table

| URL | Route name | Conceptual responsibility | Access/auth | SEO and empty behaviour |
|---|---|---|---|---|
| `/` | `home` | Home controller/view composition | Public / none | Index, self-canonical |
| `/cars` | `cars.index` | Blade shell + future Livewire inventory query | Public / none | Clean and unfiltered pagination index; filters `noindex`; empty state remains useful |
| `/cars/make/{make-slug}` | `cars.make.show` | Curated make projection | Public when taxonomy exists | Publication-gated; known empty `noindex`; unknown 404 |
| `/cars/body-type/{body-type-slug}` | `cars.body-type.show` | Curated body-type projection | Public when taxonomy exists | Same gate as make |
| `/cars/category/{category-slug}` | `cars.category.show` | Deferred curated category projection | Future | 404 until curated/published |
| `/cars/recently-sold` | `cars.recently-sold` | Deferred Sold collection | Future | 404 until useful/published |
| `/cars/{car-slug}` | `cars.show` | Public car projection | Status-dependent / none | Available/Reserved/Sold 200; Draft 404; Archived 410 or manual relevant redirect |
| `/about` | `pages.about` | Fixed-key page | Public / none | Index, self-canonical |
| `/how-to-buy` | `pages.how-to-buy` | Fixed-key buying process | Public / none | Index, self-canonical |
| `/reservation` | `pages.reservation` | Reservation overview + settings | Public / none | Index, self-canonical |
| `/nationwide-delivery` | `pages.nationwide-delivery` | Delivery overview | Public / none | Index, self-canonical |
| `/car-stands` | `car-stands.index` | Two-stand overview | Public / none | Index, self-canonical |
| `/car-stands/iju` | `car-stands.iju` | Iju stand projection | Public / none | Index, self-canonical; approved record required |
| `/car-stands/ogunnisi-road` | `car-stands.ogunnisi-road` | Ogunnisi Road projection | Public / none | Same as Iju |
| `/contact` | `pages.contact` | Contact/settings projection | Public / none | Index, self-canonical |
| `/faqs` | `faqs.index` | Published FAQ query | Public / none | Index only when substantive; otherwise `noindex` |
| `/guides` | `guides.index` | Published article index | Public / none | Index when content exists; self-canonical |
| `/guides/{article-slug}` | `guides.show` | Published guide projection | Public / none | Published 200/index; otherwise 404 |
| `/reservation-policy` | `policies.reservation` | Fixed-key approved policy | Public / none | Index after approval; self-canonical |
| `/privacy-policy` | `policies.privacy` | Fixed-key approved policy | Public / none | Index after approval; self-canonical |
| `/terms` | `policies.terms` | Fixed-key approved terms | Public / none | Index after approval; self-canonical |
| `/admin` | Filament-managed | Private panel and future auth | Authenticated authorized users | `noindex,nofollow`; never in XML sitemap |

Framework health route `/up` remains operational infrastructure, not a navigation or sitemap page. Future XML sitemap endpoints are specified in [seo-requirements.md](seo-requirements.md#xml-sitemaps).

## Route order and reserved segments

Register the Cars namespace from most specific to dynamic:

```text
/cars
/cars/recently-sold
/cars/make/{make-slug}
/cars/body-type/{body-type-slug}
/cars/category/{category-slug}
/cars/{car-slug}
```

`recently-sold` would otherwise match the one-segment car route. Reserve `make`, `body-type`, `category`, and `recently-sold` so none can become a car slug. Register future fixed guide paths before `/guides/{article-slug}`. Do not add a top-level catch-all.

All slugs use lowercase ASCII letters, digits, and internal hyphens. Keep a published car/article slug stable. A necessary correction adds the old path to a redirect registry and issues one permanent redirect to the new canonical, avoiding redirect chains.

Explicit car-stand paths are intentional. If they later share a dynamic binding, constrain it to approved public stand records and preserve these canonical slugs.

## Binding and response rules

- Public route binding must not expose soft-deleted, Draft, or administrative data.
- Unknown car, taxonomy, category, or article slugs return 404.
- A known but empty curated taxonomy returns 200 with `noindex,follow`, alternatives, and contact actions only if the taxonomy itself is published; otherwise it returns 404.
- A previously published Archived car returns 410 unless an administrator has selected a strongly relevant replacement.
- Out-of-range page numbers return 404. `?page=1` permanently normalizes to the clean route; page 2+ remains a distinct URL.
- Invalid known inventory filter values produce a controlled `noindex` zero/error state; they must never silently expose unfiltered results.
- Method semantics remain read-only for public pages. All future admin mutations use authenticated POST/PUT/PATCH/DELETE actions with CSRF and authorization.

## URL and canonical rules

- Production uses one approved HTTPS host, lowercase paths, and no trailing slash except `/`.
- Static/detail/qualified landing routes self-canonicalize.
- Tracking parameters are removed from canonical construction.
- Search, filter, and sort combinations are `noindex,follow` and canonicalize to `/cars`; unfiltered page 2+ self-canonicalizes.
- Internal links, redirects, XML sitemaps, Open Graph URLs, and canonicals use the same URL generator and host.
- Do not redirect every Sold car to a generic page. Follow the reviewed retention flow in [seo-requirements.md](seo-requirements.md#sold-car-strategy).

## Query-string contract

The inventory route accepts only these public keys:

| Key | Value contract |
|---|---|
| `q` | Trimmed human search; never sent raw to analytics |
| `make`, `model`, `body_type`, `car_stand` | One approved lowercase slug; model must belong to make |
| `year_min`, `year_max` | Valid four-digit integers |
| `price_min`, `price_max` | Whole NGN amounts, digits only |
| `transmission`, `fuel_type` | One controlled lowercase value |
| `mileage_min`, `mileage_max` | Non-negative integers in the normalized query unit |
| `availability` | Omitted/`available` or explicit `reserved` |
| `sort` | `latest`, `price_asc`, `price_desc`, `year_desc`, `mileage_asc` |
| `page` | Integer 2+; page 1 omitted |

Canonical query serialization uses the order shown above, omits defaults and unsupported keys, resets `page` on filter/sort changes, and rejects inverted ranges with an accessible validation message. These rules are shared by Blade, Livewire, analytics normalization, testing, and link generation.

## Admin route principles

Filament owns `/admin` after installation. There is no public registration. Unauthenticated access enters the future login flow; an authenticated user without panel access receives 403. Resource and custom-action policies apply on every request, and the admin host/path is still protected even though it is excluded from search.
