# Analytics event implementation

Analytics measures website interactions, not off-site outcomes. A WhatsApp, telephone, or directions click does not prove a conversation, qualified lead, visit, reservation, purchase, or delivery.

The public layout emits one non-PII analytics configuration object and `resources/js/app.js` provides one delegated event layer. The provider is configurable and disabled by default. Core navigation, contact links, and forms work when JavaScript, storage, consent, or the analytics provider is unavailable.

## Supported events

| Event | Trigger | Allowlisted context |
|---|---|---|
| `view_car` | A normal public Available, Reserved, or Sold vehicle detail page renders | vehicle identifiers/facts, `page_type`, `source_context` |
| `whatsapp_click` | A valid WhatsApp link is activated | page/vehicle context and `cta_position` |
| `phone_click` | A `tel:` link is activated | page/vehicle context and `cta_position` |
| `directions_click` | A configured map/directions link is activated | `page_type`, `cta_position` |
| `enquiry_submitted` | The server has validated and stored a general or vehicle enquiry, then returns the accepted redirect | safe vehicle context, `page_type`, `enquiry_type` |
| `select_related_car` | A related vehicle card is activated | safe selected/source context |

Supported parameters are `event_uuid`, `car_id`, `stock_number`, `make`, `model`, `year`, `status`, `page_type`, `cta_position`, `source_context`, and `enquiry_type`. String values are capped in the client layer. Names, phone numbers, email addresses, enquiry messages, company fields, raw search terms, full URLs, and form payloads are never included.

`view_car` is not fired for an authenticated preview. Contact links use delegated handling so pointer and keyboard activation take the same path. A server-accepted enquiry carries a generated UUID in one-time flash data; session storage prevents the accepted event from being counted again during repeated client callbacks. Failed validation and rejected submissions do not receive a success event.

The layer exposes `window.AutoMercyAnalytics.track(name, parameters)` and dispatches `auto-mercy:analytics` browser events. Sanitized payloads are also appended to `window.autoMercyAnalyticsQueue`, which makes provider-independent automated checks possible.

## Campaign attribution

The arrival layer recognizes only `utm_source`, `utm_medium`, `utm_campaign`, `utm_term`, `utm_content`, `gclid`, `dclid`, `gbraid`, `wbraid`, `fbclid`, and `msclkid`. It captures those values in session storage for later provider use and preserves them through inventory normalization redirects. Campaign values do not become inventory filters and are excluded from canonical URLs, Open Graph URLs, sitemaps, and internal links.

No arbitrary query parameters or personal form fields are accepted as attribution.

## Provider, consent, and activation

The current adapter supports GA4 and is off unless all required configuration is present:

```dotenv
ANALYTICS_PROVIDER=ga4
ANALYTICS_ENABLED=true
ANALYTICS_MEASUREMENT_ID=G-XXXXXXXXXX
ANALYTICS_REQUIRE_CONSENT=true
```

When consent is required, no GA script loads and no external event dispatch occurs until the application records `granted` through `window.AutoMercyAnalytics.setConsent('granted')`. The existing application does not yet have approved privacy wording or a confirmed consent UI, so production dispatch must remain disabled until those requirements are resolved. `setConsent('denied')` leaves all customer features available.

The repository does not invent a measurement ID and does not persist analytics events internally. If internal reporting is later approved, it needs a separately reviewed retention, consent, validation, rate-limiting, and privacy design.

## Production verification

After privacy approval and a real measurement ID are available:

1. Configure the environment, rebuild the config cache and production assets, and verify the consent decision survives normal navigation.
2. Use GA4 DebugView and browser network tools to check each supported event once from every relevant CTA.
3. Confirm rejected forms never emit `enquiry_submitted`, accepted redirects emit it once, and no PII or full campaign URL appears in payloads.
4. Test unknown/denied consent, ad blocking, offline navigation, back/forward cache, and refresh behavior. Missing analytics must never block the customer action.
5. Label reports as views, clicks, and accepted enquiries. Do not rename these metrics as conversations, leads qualified by staff, visits, or sales.

Search Console is configured separately and does not require interaction events.
