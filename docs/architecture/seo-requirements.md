# SEO requirements

SEO is part of the server-rendered page contract. Visible title, price, availability, car facts, metadata, internal links, and JSON-LD must read the same current records so a status change cannot leave contradictory output.

## Indexation and canonical matrix

| URL class | Robots | Canonical | XML sitemap |
|---|---|---|---|
| Approved static public page | `index,follow` | Self | Yes |
| Clean `/cars` | `index,follow` when useful inventory exists | Self | Yes |
| Unfiltered `/cars?page=2+` | `index,follow` | Self, including page | Optional; details are primary discovery URLs |
| Filter, search, or non-default sort | `noindex,follow` | Clean `/cars` | No |
| Qualified make/body/category landing | `index,follow` only after publication gate | Self | Yes |
| Known published but currently empty landing | `noindex,follow` | Self | No |
| Available or Reserved car | `index,follow` | Self | Yes |
| Sold car during retention | `index,follow` | Self | Yes while indexable |
| Draft | Not served publicly (404) | None | No |
| Archived | 410 or a manually selected relevant redirect | None or redirect target | No |
| Admin | `noindex,nofollow` plus authentication | None | No |
| Unknown record/out-of-range page | 404 | None | No |

Filtered pages must remain crawlable long enough for the `noindex` directive to be read; do not rely on a conflicting broad `robots.txt` block. Monitor parameter crawling in Search Console before changing crawl controls. Google advises controlling faceted URL spaces and using separate self-canonicals for real pagination pages rather than canonicalizing all pages to page 1: [faceted navigation](https://developers.google.com/crawling/docs/faceted-navigation), [robots meta](https://developers.google.com/search/docs/crawling-indexing/robots-meta-tag), and [pagination](https://developers.google.com/search/docs/specialty/ecommerce/pagination-and-incremental-page-loading).

## Canonical URL rules

- Use one approved HTTPS host, absolute URLs, lowercase paths, and no trailing slash except `/`.
- Tracking parameters never appear in canonicals, Open Graph URLs, internal links, or sitemap entries.
- `?page=1` normalizes to the clean route. Unfiltered page 2+ self-canonicalizes.
- Search/filter/sort pages canonicalize to `/cars`; filtered pagination follows the same rule and remains `noindex`.
- Curated landing pagination self-canonicalizes only after that landing passes its publication gate.
- A changed published slug issues one permanent redirect from a recorded former slug; avoid chains.
- Sitemaps, internal links, HTTP redirects, and canonical elements must agree. Canonicals are signals rather than guaranteed directives ([Google canonical guidance](https://developers.google.com/search/docs/crawling-indexing/consolidate-duplicate-urls)).

`canonical_override` is exceptional: only a Super Administrator may set a validated same-site HTTPS URL, with a reason, preview, and audit. Most pages derive self-canonicals.

## Metadata

Every indexable page needs a unique server-rendered `<title>`, meta description, canonical, Open Graph title/description/URL/type, and sharing image when an approved asset exists. Add Twitter/X card metadata where appropriate without implying that an account exists.

Recommended patterns (final copy remains editable and length-checked):

```text
{Year} {Make} {Model} {Trim} for Sale in Lagos | Auto Mercy
{Make} Cars for Sale in Lagos | Auto Mercy
{Body Type} Cars for Sale in Lagos | Auto Mercy
Auto Mercy Iju Car Stand | Lagos
{Guide Title} | Auto Mercy
Sold: {Year} {Make} {Model} {Trim} | Auto Mercy
```

Descriptions use only visible facts such as status, price, mileage, condition, assigned car stand, inspection/contact actions, and nationwide delivery. Avoid duplicated boilerplate and unsupported claims. Sold titles/descriptions state Sold. The car's actual primary image is its Open Graph image. A generic sharing asset and organization-logo output wait for approved production variants.

## Structured data

Emit JSON-LD in the initial HTML and validate against both Schema.org and Google's Rich Results Test. Markup cannot add facts that are absent from visible content.

| Page | Graph and decision |
|---|---|
| Home/About | One `Organization`: `name` Auto Mercy, `legalName` Auto Mercy of God Nigeria Limited, approved URL/email/contact and logo only when eligible |
| Contact/Car Stands | Organization plus two stable `AutoDealer` location nodes using visible approved facts |
| Car-stand detail | One `AutoDealer` node with exact address and Monday–Saturday 08:00–18:00, plus breadcrumb |
| Car detail | Dual `@type: ["Product", "Car"]`, nested `Offer`, and `BreadcrumbList` |
| Guide detail | `Article` with real author/publisher/dates/image plus `BreadcrumbList` |
| Other lower pages | `BreadcrumbList` only when the matching visible trail exists |
| FAQs | Omit `FAQPage` at launch; reconsider only if search guidance/product value changes |

`AutoDealer` is the most specific suitable Schema.org subtype below `AutomotiveBusiness`/`LocalBusiness` ([Schema.org AutoDealer](https://schema.org/AutoDealer)). Model each car stand with its own stable `@id` and canonical page; relate both to the single organization without implying separate legal entities.

### Car/offer mapping

| Schema property | Source / rule |
|---|---|
| `name` | Visible year/make/model/trim title |
| `sku` | Immutable stock number |
| `brand` | Actual vehicle make—not the dealership |
| `model`, `vehicleModelDate` | Actual model and year |
| `mileageFromOdometer` | Actual value and matching unit |
| `fuelType`, `vehicleTransmission`, colours | Visible stored facts only |
| `itemCondition` | `UsedCondition` |
| `image` | Crawlable actual car image URLs |
| `offers.price`, `priceCurrency` | Visible integer price, `NGN` |
| `offers.url`, `seller` | Canonical car URL and Auto Mercy organization node |
| `offers.availability` | Available → `InStock`; Reserved/Sold → `OutOfStock` |

Product-snippet markup fits a single-car page where contact happens off-site. Google notes that `Car` is not automatically treated as a `Product`, so dual typing is appropriate where Product eligibility is intended ([Google Product snippet guidance](https://developers.google.com/search/docs/appearance/structured-data/product-snippet)). Do not emit purchase/checkout properties the site cannot fulfil. Do not add ratings, reviews, or aggregate ratings.

Google generally limits FAQ rich-result display to authoritative government and health sites. Semantic visible FAQs remain useful, but launch markup provides no expected benefit ([Google FAQ update](https://developers.google.com/search/blog/2023/08/howto-faq-changes)).

## Curated landing pages

Make, body-type, and future selected category pages are database-backed editorial publications, not automatic faceted pages. A page can be indexable only when it has:

- relevant Available inventory;
- a unique useful H1 and introduction;
- unique title and description;
- canonical URL and visible breadcrumbs;
- meaningful links to cars, related guides, and broader inventory;
- enough sustained customer/search value to avoid thin content.

If a known published taxonomy temporarily reaches zero Available cars, keep a useful 200 page with `noindex,follow`, an honest message, alternatives, and contact actions; remove it from the sitemap. Unknown/unpublished taxonomy returns 404. Do not publish arbitrary combinations or artificial location pages.

## Sold-car strategy

1. On transition to Sold, keep the canonical detail URL at 200 and initially indexable.
2. Immediately show Sold visibly, remove it from all Available collections and hold actions, and change `Offer` availability to `OutOfStock`.
3. Keep factual content, actual images, and up to four related Available cars.
4. Retain the URL in the cars sitemap while it remains indexable.
5. Create a manual review task after 90 days; age alone does not auto-archive.
6. Keep permanently when unique content, backlinks, impressions, or inventory-history value justify it.
7. Redirect only when an administrator selects a strongly relevant replacement. Never send every Sold car to Home.
8. Otherwise use an optional interim 200 `noindex,follow` deindexing period, then archive to 410.

This preserves bookmarks and earned relevance without misleading customers. Search Console data informs review, but the retention period remains a policy recommendation pending operational approval.

## Local SEO

- Use only the supplied addresses for Iju and Ogunnisi Road. Do not invent coordinates, postal codes, access landmarks, or map URLs.
- Use the approved Monday–Saturday 8:00 AM–6:00 PM hours.
- Display the approved local number. Add structured/deep-link `telephone` only after the intended international format is approved; Google recommends country and area codes in organization data.
- Add `sameAs` only for verified canonical profile URLs.
- `public/logo.jpeg` now exists, but logo structured data and general production use wait for provenance/production approval and a crawlable suitable variant. Google's current organization logo guidance requires a crawlable supported image at least 112×112 ([Organization guidance](https://developers.google.com/search/docs/appearance/structured-data/organization)).
- Each car-stand page needs distinct address/access content, actual media, assigned inventory, and useful visit information. Google Business Profile setup/verification is an external launch task.
- Follow current [LocalBusiness guidance](https://developers.google.com/search/docs/appearance/structured-data/local-business) and never add fabricated review markup.

## XML sitemaps

Planned endpoints:

```text
/sitemap.xml
/sitemaps/pages.xml
/sitemaps/cars.xml
/sitemaps/guides.xml
/sitemaps/car-stands.xml
```

The root file is a sitemap index. Curated make/body/category pages may live in `pages.xml` initially. Include only public canonical indexable URLs; exclude query filters/search/sort, Draft, Archived, admin, error responses, and unqualified/empty landings. Include Sold cars only during their indexable period. `lastmod` changes only for meaningful visible price, status, copy, or media changes. Use absolute production URLs, reference `/sitemap.xml` from `robots.txt`, and submit only the index to Search Console. Google recommends listing preferred canonical URLs ([sitemap guidance](https://developers.google.com/search/docs/crawling-indexing/sitemaps/build-sitemap)).

## Image SEO

- Use actual car media with descriptive filenames such as `2019-toyota-camry-xse-front.jpg`.
- Alt text identifies the actual car and view; describe a visible notable detail where useful. Decorative assets have empty alt.
- Include intrinsic width/height and responsive sources. Keep a stable primary URL.
- Provide modern WebP/AVIF where processing support is verified, with JPEG fallback.
- Do not lazy-load the LCP primary image; lazy-load below-fold gallery media.
- Ensure media URLs are crawlable when the car is public. Optional image sitemap entries can be evaluated after launch data.

## Internal linking and breadcrumbs

- Home links to Available Cars, How to Buy, both car stands, and real current cars.
- Car details link to qualified make/body pages, the assigned car stand, Reservation Policy, and related Available cars.
- Qualified landings link to actual car details and relevant guides.
- Car-stand pages may link to their user-facing filtered inventory URL, which remains `noindex`.
- Guides link contextually to canonical inventory/landing pages. Footer links all core business and legal pages.
- Never link internally to tracking-parameter variants.
- Visible breadcrumb paths and `BreadcrumbList` match; follow [Google breadcrumb guidance](https://developers.google.com/search/docs/appearance/structured-data/breadcrumb).

## Search Console launch checklist

- Verify a Domain property and production HTTPS variants.
- Submit `/sitemap.xml` and confirm processing.
- Inspect representative static, Available, Reserved, Sold, stand, guide, curated, and paginated URLs.
- Monitor Page Indexing, selected canonicals, crawl stats, Core Web Vitals, sitemap coverage, Product/Breadcrumb enhancements, security issues, and manual actions.
- Review query-parameter discovery before tightening `robots.txt`.
- Validate server-rendered JSON-LD, then re-test after status/price changes.
- Confirm status, visible copy, metadata, Open Graph and structured data change from the same transaction/cache invalidation.
