# Analytics event specification

Analytics measures website interactions, not off-site outcomes. “WhatsApp click,” “phone click,” and “directions click” do not prove a conversation, connected call, visit, reservation, purchase, or delivery.

GA4 is approved as the analytics direction but is not configured. Tag loading, storage, and event dispatch remain behind the privacy/consent decision. Search Console is configured externally and does not need interaction events.

## Event dictionary

| Event | Trigger and pages | Allowlisted parameters | GA4 | Internal |
|---|---|---|:---:|:---:|
| `view_car` | Successful Available/Reserved/Sold detail navigation, once after canonical record renders | `car_id`, `stock_number`, make/model/year/status, `car_stand`, `page_type`, `source_context` | Yes | Yes, if approved |
| `search_cars` | User commits a non-empty search and final result count is known on `/cars` | `search_type` (never raw text), `result_count`, `sort`, `page_type` | Yes | No |
| `filter_cars` | User applies a changed normalized filter state and final result count is known | normalized allowlisted filters, `active_filter_count`, `result_count`, `sort` | Yes | No |
| `whatsapp_click` | User activates an actual WhatsApp destination from header, card, detail, stand, contact, or empty state | car fields when applicable, `car_stand`, `page_type`, `cta_position` | Yes | Yes |
| `phone_click` | User activates an actual `tel:` link | car fields when applicable, `car_stand`, `page_type`, `cta_position` | Yes | Yes |
| `directions_click` | User activates an approved external directions link | `car_id`/stock when applicable, `car_stand`, `page_type`, `cta_position` | Yes | Yes |
| `share_car` | Native share succeeds or copy-link fallback confirms successful copy | car fields, `share_method`, `page_type`, `cta_position` | Yes | Yes |
| `view_reservation_terms` | Standalone Reservation/Policy page renders, or detail disclosure opens first time in that navigation | car fields when applicable, `page_type`, `content_position` | Yes | No |
| `view_delivery_information` | Delivery page renders, or detail disclosure opens first time | car fields when applicable, `page_type`, `content_position` | Yes | No |
| `view_car_stand` | Iju or Ogunnisi Road detail page renders once per navigation | `car_stand`, `page_type`, `source_context` | Yes | No |
| `select_related_car` | User activates a related-car card from a detail/empty/Sold recovery context | selected car fields, `source_car_id`, `source_context`, `card_position` | Yes | Yes |

Standard GA4 `page_view` may also be enabled through base configuration. Do not manually fire it again if enhanced measurement already owns the same navigation. `select_related_car` records the selection; the destination separately fires `view_car` after successful render.

## Parameter contract

| Parameter | Format and privacy rule |
|---|---|
| `car_id` | Internal numeric ID serialized as a string, or omit when no car |
| `stock_number` | Stable `AM-0001` format |
| `make`, `model`, `car_stand`, `status` | Controlled lowercase slug/value |
| `year`, `result_count`, `active_filter_count`, `card_position` | Non-negative integer |
| `page_type` | Controlled value: `home`, `inventory`, `car_detail`, `car_stand`, `contact`, `reservation`, `delivery`, `guide`, `empty_state` |
| `cta_position` | Controlled value such as `header`, `card`, `hero`, `sticky_mobile`, `specifications`, `footer`, `empty_state` |
| `source_context` | Controlled navigation origin; never a referrer URL containing query data |
| normalized filters | Slugs/integers for the public filter keys; exclude `q` and default values |
| `sort` | One approved sort value |
| `share_method` | `native` or `copy_link` |

Do not send names, phone numbers, email addresses, WhatsApp text, conversation content, raw free-text search, full destination/referrer URLs, IP addresses, precise location, administrator identity, or an identifier that permanently identifies a person/device. GA4 policy prohibits personally identifiable information, including data accidentally placed in URLs/titles ([Google PII guidance](https://support.google.com/analytics/answer/6366371?hl=en)).

Event/parameter names use lowercase letters and underscores, start with a letter, and stay within GA4's 40-character limit. Parameter values stay within the standard-property limit; no reserved `ga_`, `google_`, or `firebase_` prefixes. Current limits are documented in [GA4 event collection limits](https://developers.google.com/analytics/devguides/collection/protocol/ga4/sending-events).

## Trigger rules and duplicate prevention

1. Use one delegated client handler/event-dispatch service. Components emit an intent; they do not each call GA4 and the internal endpoint independently.
2. Generate one `event_uuid` per accepted interaction and reuse it for the GA4/internal branches. The internal table has a unique constraint.
3. Generate a navigation ID. `view_car`, `view_car_stand`, and disclosure-view events fire once per navigation/content item, not after Livewire re-renders, bfcache restores, or focus changes.
4. Compare committed canonical inventory state. `filter_cars` fires after Apply only when the normalized fingerprint changes and results settle; `search_cars` fires after explicit submit. Never fire on each keystroke or drawer staging.
5. A click event fires only for a primary-button activation that is about to use a valid destination. Keyboard and pointer paths pass through the same handler.
6. `share_car` fires only after the share promise succeeds or clipboard copy succeeds; cancellation is not success.
7. Retry of an internal request reuses the UUID. The server returns idempotent success for an existing UUID rather than inserting again.
8. Test with GA4 DebugView and browser/network assertions. Treat ad-blocking, offline mode, consent denial, and navigation interruption as expected data loss—not a reason to delay the user's action.

## Internal event storage

Internal storage is optional and limited to operational dashboard metrics that GA4 access/retention may not reliably provide: `view_car`, contact/directions/share clicks, and related-car selection. Search/filter/content-view events remain GA4-only to reduce duplication and database volume.

Planned row: unique `event_uuid`, event name, nullable car/car-stand foreign keys, controlled page/CTA fields, small allowlisted metadata JSON, consent state, and UTC `occurred_at`. Do not store Laravel session ID, GA client ID, IP, full user agent, or raw URL. Apply uniqueness and the indexes listed in [data-model.md](data-model.md#indexes-and-query-integrity).

Internal views/clicks are aggregate product signals. Rate-limit the write endpoint, verify same-site CSRF/origin expectations as applicable, validate the allowlist, and flag obvious non-human/duplicate traffic without building user profiles. Retention duration and whether consent is required are unresolved until privacy review; do not enable persistence before that decision.

## Consent and configuration

- Decide, with qualified privacy review, whether GA4 and each internal measurement category can load before consent in the target deployment. Document the lawful basis and update Privacy Policy before launch.
- Default to no analytics dispatch until the consent state is known. A declined choice must preserve all core website functions.
- Store the public GA measurement ID in environment configuration. It is not editable in content settings. No Measurement Protocol secret is needed for the planned browser events.
- Configure GA4 data retention, unwanted-referral/cross-domain behaviour (if any), internal traffic rules, enhanced measurement, and data redaction deliberately.
- Never use analytics consent as consent to contact a person or reuse customer information.

## Reporting

GA4 reports traffic and navigation; internal widgets report stored views/clicks. Define custom dimensions only for parameters that answer a named business question. Dashboard tables always show period, event name, count, and data source.

Do not calculate a “conversion rate” to sale because the website has no reliable join to off-site outcomes. A contact-click rate may be reported as clicks divided by detail views, clearly labelled. Counts can differ between GA4 and internal data due to consent, blocking, bots, retries, time zones, retention, and event rules; differences are expected and must be documented rather than merged.

## Verification matrix

- Each event fires from every documented CTA position with the correct controlled fields.
- No event fires on cancelled share, staged filter, failed page render, or invalid destination.
- Re-render/back-forward/retry tests produce the expected single event.
- URLs, titles, parameters, and DebugView contain no prohibited personal data or raw search.
- Consent denied/unknown prevents the configured branch without blocking navigation.
- Internal duplicate UUID returns success without incrementing counts.
- GA4 and admin labels use the exact names in this document.
