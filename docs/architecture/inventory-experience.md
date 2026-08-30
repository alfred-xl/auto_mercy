# Inventory experience

## Rendering model

Blade server-renders the initial `/cars` result set, filter form, metadata, breadcrumbs, result count, cards, empty state, and real pagination anchors. Future Livewire progressively enhances the GET form and results region. Search, filtering, detail navigation, and pagination remain usable without JavaScript.

The default query returns only published Available cars, ordered by latest arrival. Reserved cars appear only when `availability=reserved` is explicit. Sold cars are excluded from active inventory; Draft and Archived records never appear.

## Filter contract

| Filter | UI and data rule | URL key |
|---|---|---|
| Search | Search year, make, model, trim, and exact/prefix stock number; submit explicitly | `q` |
| Make | Single published make slug | `make` |
| Model | Single model belonging to selected make; changing make clears an incompatible model and announces it | `model` |
| Year | Valid integer minimum/maximum within sensible inventory bounds | `year_min`, `year_max` |
| Price | Whole NGN range; UI displays ₦ and separators, URL uses digits | `price_min`, `price_max` |
| Body type | Single structured taxonomy slug | `body_type` |
| Transmission | Single controlled value | `transmission` |
| Fuel type | Single controlled value | `fuel_type` |
| Mileage | Non-negative range queried in a normalized unit; display retains recorded unit | `mileage_min`, `mileage_max` |
| Car stand | `iju` or `ogunnisi-road` from active data | `car_stand` |
| Availability | Omitted/`available` or explicit `reserved` | `availability` |

Desktop shows Make, Model, Year, Price, Body type, Transmission, and Car stand in the primary filter area; Fuel, Mileage, and Availability sit under More Filters. Mobile places all filters in a labelled drawer. Both use explicit **Apply Filters**. Mobile Cancel discards staged changes; desktop Reset restores the last committed state. Search submits rather than firing on each keystroke. Sort commits immediately.

Invalid min/max ranges produce a persistent accessible message and do not apply. Unsupported keys are ignored and omitted from generated URLs. An invalid known value yields a controlled `noindex` error/zero state, never an unfiltered result set.

## Sorting

| Value | Meaning and deterministic order |
|---|---|
| `latest` | `published_at` descending, then ID descending |
| `price_asc` | price ascending, then `published_at` descending, ID descending |
| `price_desc` | price descending, then `published_at` descending, ID descending |
| `year_desc` | year descending, then `published_at` descending, ID descending |
| `mileage_asc` | normalized mileage ascending, then `published_at` descending, ID descending |

Default `latest` is omitted from the URL. The tie-breakers prevent records moving, duplicating, or disappearing unpredictably between pages.

## URL, browser history, and active state

- Committed controls serialize in the canonical order defined in [routes.md](routes.md#query-string-contract), with defaults omitted.
- Applying filters, changing sort, removing a chip, clearing all, and changing page creates a restorable history state. Rapid equivalent updates use replace rather than creating duplicates.
- Back/Forward restores form controls, result count, results, pagination, and scroll/focus context where practical.
- Human-readable filter chips appear above results. Each has an accessible remove name; removal resets `page`.
- Clear all returns to `/cars` with default availability and sorting.
- URLs are shareable. Query filters/search/sort are `noindex,follow` and canonicalize to `/cars`; unfiltered page 2+ self-canonicalizes.
- Raw free-text search is not sent to analytics because it may contain personal information.

Example: `/cars?make=toyota&body_type=suv&price_max=30000000`.

## Loading, errors, and result announcements

During an enhanced request, preserve the current layout, mark the results region `aria-busy=true`, disable only controls whose repeated submission is unsafe, and show a restrained progress indication. On completion, announce the result count through a polite live region and keep focus on the initiating control. Avoid skeleton geometry that shifts content.

A valid zero result is different from a server failure. A failure preserves committed filters, explains that results could not load, and offers Retry plus normal navigation.

### Empty results

Filtered zero results show active criteria, remove-one actions, Clear all, and transparently labelled related Available cars selected by progressively relaxing criteria. They also offer Call and WhatsApp without promising a sourcing or notification service.

If no Available cars exist globally, say so plainly and invite the customer to confirm current availability. Never fill this state with Sold records presented as current stock.

## Pagination

- Start with 12 cards per page, subject to representative content/performance testing.
- Render Previous, Next, and numbered links with an accessible current-page state and descriptive labels.
- Preserve valid filters/sort. Any filter/sort change resets to page 1.
- `?page=1` normalizes to the clean URL; page 2+ is self-canonical when unfiltered.
- Out-of-range pages return 404. If a Livewire update reduces the total page count, return to page 1 and announce it.
- Do not implement infinite scroll as the only navigation method.

## Car cards

Cards use a consistent 4:3 image area and contain: actual primary image/fallback, text availability status, year/make/model, optional trim, formatted price, a short essential metadata row, assigned car stand, and **View Details**. A WhatsApp action may appear only where it stays visually and accessibly distinct. Use no colour-only status or unsupported urgency message.

Fallback media is an approved neutral brand asset that states imagery is unavailable; it is not a stock vehicle photograph. Its alternative text does not pretend to depict the car.

## Car-detail contract

Order content for evaluation and contact:

1. Breadcrumb.
2. Responsive gallery and optional genuine video.
3. Year, make, model, optional trim, stock number, publication/availability state.
4. Price and primary Call/WhatsApp actions.
5. Key specifications.
6. Assigned car stand and directions.
7. Description and structured features.
8. Full specifications: mileage/unit, body type, transmission, fuel, engine, drivetrain, colours, condition.
9. Complete reservation terms.
10. Nationwide delivery statement and contact action.
11. Related Available cars.
12. Share action.

Missing optional facts are omitted, not rendered as misleading values. Required publication fields are listed in [data-model.md](data-model.md#publication-invariant). The optional video is a validated HTTPS URL from an approved host, does not autoplay, and does not block core content.

### Status-specific behaviour

| Status | Browse visibility | Detail response | Actions |
|---|---|---|---|
| Draft | None | 404 | None |
| Available | Default | Public/indexable | Enquire, reservation information, directions |
| Reserved | Explicit availability filter only | Public, clearly Reserved | Remove hold action; allow enquiry about status/alternatives |
| Sold | None | Public initially, clearly Sold | Remove hold/availability claims; show related Available cars |
| Archived | None | 410 or manually selected relevant redirect | None |

## Gallery and mobile behaviour

- Primary image loads eagerly/high-priority when it is the LCP candidate; below-fold media lazy-loads.
- Images have intrinsic dimensions, responsive variants, concise unique alt text, and no autoplay motion.
- Thumbnails and next/previous controls are keyboard-operable with current-image state. A full-screen dialog traps/restores focus, closes on Escape, and has an accessible name.
- Mobile specifications may use native/accessible disclosures; essential price/status/contact remain expanded.
- A detail page has at most two sticky actions: Call and WhatsApp. Use safe-area padding and page-bottom space so actions cover neither content nor browser UI.
- Native share records success; copy-link fallback confirms completion in an accessible status message.

## Related-car selection

Only published Available cars qualify. Exclude the current car and deduplicate. Rank deterministically: same model/make; same make; same body type; then recently published Available cars. Within a tier, prefer a similar price band and optionally the same car stand, then latest/ID. Return at most four initially. If none qualify, show Browse Cars and contact actions rather than an empty carousel.

## Search and query performance

Start with MySQL relational filters and controlled text matching; no external search service is justified. Eager-load make, model, body type, stand, and primary image. Select only card columns; use the indexes in [data-model.md](data-model.md#indexes-and-query-integrity), query-count tests, representative `EXPLAIN`, and production slow-query monitoring before adding more indexes or infrastructure.
