# SEO implementation

SEO is part of the server-rendered public-page contract. Auto Mercy is one dealership with two physical locations and one shared inventory. A vehicle has one record, one management workflow, and one public URL; neither staff nor customers assign stock to a location. Customers are asked to confirm the viewing location before visiting.

## Indexing and canonical policy

| URL class | Robots | Canonical | Sitemap |
|---|---|---|---|
| Home, Services, Contact | `index,follow` | Self | Yes |
| `/cars` with public inventory | `index,follow` | Self | Yes |
| `/cars?page=1` | 301 to `/cars` | N/A | No |
| Unfiltered `/cars?page=2+` | `index,follow` | Self, including `page` | No |
| Search, filter, or alternative sort | `noindex,follow` | Normalized self URL | No |
| Published curated landing with matching inventory | `index,follow` | Self, including page 2+ | Yes |
| Published curated landing temporarily empty | `noindex,follow` | Self | No |
| Available, Reserved, or retained Sold vehicle | `index,follow` | Self | Yes |
| Draft / unknown / unpublished landing / out-of-range page | 404 | None | No |
| Archived vehicle | 410 | None | No |
| Authenticated staff preview | `noindex,nofollow` | Public vehicle URL | No |

Filtered results deliberately use normalized self-canonicals because different filters can produce materially different result sets. They remain `noindex,follow`; the application does not falsely identify every filtered page as a duplicate of `/cars`. `robots.txt` does not block these URLs, so crawlers can read the directive. Google treats canonical declarations as signals and recommends crawl controls for large faceted spaces: [canonical guidance](https://developers.google.com/search/docs/crawling-indexing/consolidate-duplicate-urls), [faceted navigation](https://developers.google.com/crawling/docs/faceted-navigation), and [robots meta](https://developers.google.com/search/docs/crawling-indexing/robots-meta-tag).

Only inventory keys defined by `CarInventoryQuery` affect results. The explicit campaign allowlist is `utm_source`, `utm_medium`, `utm_campaign`, `utm_term`, `utm_content`, `gclid`, `dclid`, `gbraid`, `wbraid`, `fbclid`, and `msclkid`. These values survive a necessary normalization redirect but never enter canonical URLs, Open Graph URLs, sitemap entries, or generated internal links. Unsupported query parameters are removed by a permanent normalization redirect.

Canonical and sitemap URLs use `SEO_BASE_URL`, falling back to `APP_URL`. Production must configure the approved HTTPS origin; the repository intentionally does not guess it and does not force a hostname redirect. Set `TRUSTED_PROXIES` to a comma-separated allowlist only when the deployment is actually behind those proxies. This keeps forwarded scheme/host handling explicit and avoids redirect loops.

## Metadata and vehicle status

The shared public layout owns title, description, robots, canonical, Open Graph, Twitter card, JSON-LD, and analytics configuration output.

- Available vehicle: `{Year} {Make} {Model} {Trim} for Sale in Lagos | Auto Mercy`.
- Reserved vehicle: `Reserved: {Year} {Make} {Model} {Trim} | Auto Mercy`.
- Sold vehicle: `Sold: {Year} {Make} {Model} {Trim} | Auto Mercy`.
- Descriptions combine verified category, body type, price, mileage when known, current status, and an inspection/contact action.
- Rich text is stripped, entities decoded, and whitespace normalized before it is used as plain text.
- The primary vehicle image is the sharing image, with stored editable alt text and a stable generic fallback.
- Sold pages retain their useful URL and facts, use `OutOfStock`, invite enquiries about similar available cars, and show available alternatives. They are excluded from active inventory collections.
- Reserved and Sold are availability labels. Brand-new, foreign-used, and pre-order remain separate listing categories.

## Structured data

JSON-LD is emitted in initial HTML.

- Home and Contact contain one `Organization` for Auto Mercy plus two `AutoDealer` physical-location nodes. Both location nodes use `parentOrganization` to reference the same organization; they do not represent separate businesses or stock pools.
- Vehicle pages contain `@type: ["Product", "Car"]`, a current `Offer`, actual vehicle fields and image URLs, the Auto Mercy seller node, and a `BreadcrumbList` matching the visible trail.
- Offer price uses the visible integer price and `NGN`. Available maps to `InStock`; Reserved and Sold map to `OutOfStock`.
- `NewCondition` is emitted only for brand-new listings. `UsedCondition` is emitted only for foreign-used listings. Pre-order does not imply a condition, so condition is omitted.
- Mileage, fuel, transmission, colours, body type, and images are emitted only when stored.
- Ratings, reviews, coordinates, warranties, delivery promises, checkout behavior, and FAQ schema are not invented.

Validate representative output against Schema.org and Google's supported rich-result tooling after production URLs are accessible. Product markup does not guarantee a rich result.

## Curated inventory landing pages

The reusable routes are `/cars/makes/{slug}`, `/cars/categories/{slug}`, and `/cars/body-types/{slug}`. They query the same `cars` table and `CarInventoryQuery` used by `/cars`; there are no copied or branch-specific collections.

Publication is explicit in `config/automercy.php`. Candidate Toyota, Lexus, foreign-used, brand-new, and SUV pages are disabled by default through environment flags. Enable one only after confirming relevant real inventory and approving its distinct copy. Published pages provide a title, description, H1, introduction, visible and structured breadcrumbs, vehicle links, related published landing links, and correct pagination. Unknown or unpublished pages return 404. A temporarily empty published page remains a useful 200 with contact options, `noindex,follow`, and no sitemap entry.

## Sitemap and images

`/sitemap.xml` is a dynamic sitemap appropriate to the current site size. It contains canonical indexable static pages, active public vehicle pages, retained Sold pages, and published non-empty curated landings. It excludes query results, pagination, drafts, archived vehicles, previews, errors, unpublished/noindex/empty landings, and admin routes. `lastmod` uses stored content, vehicle, or image-related timestamps rather than request time. Car image changes touch their vehicle, and image reordering explicitly touches the vehicle. `public/robots.txt` references the sitemap.

Vehicle cards and galleries retain the WebP variant pipeline, output stored intrinsic dimensions when available, and use responsive `srcset`/`sizes`. The visible main image is eager with high fetch priority; thumbnails and below-fold card images are lazy. Important headings and content are not hidden behind reveal animations, and navigation, forms, links, inventory results, and core copy remain server-rendered and usable without JavaScript.

## Production activation checklist

1. Set `APP_URL` and `SEO_BASE_URL` to the approved HTTPS origin. Set `TRUSTED_PROXIES` only to verified proxy addresses/CIDRs, then rebuild configuration cache.
2. Review actual production inventory and enable only supported landing flags (`SEO_LANDING_*_PUBLISHED=true`).
3. Verify both addresses, hours, telephone/WhatsApp, email, and map destinations against current business records.
4. Verify a Search Console Domain property, submit `/sitemap.xml`, inspect representative URLs, and monitor canonical selection, indexing, crawl stats, Product/Breadcrumb enhancements, and Core Web Vitals.
5. Test rendered structured data after real price/status/image changes and run live mobile performance checks from production.

Useful future buyer-guide topics may include how to inspect a used car, documents to check before buying in Nigeria, and how Auto Mercy reservations work. These should only be published later with reviewed, accurate business/legal content; no blog or generic articles are part of this implementation.
