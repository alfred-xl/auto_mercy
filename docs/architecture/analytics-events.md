# Analytics event implementation

Analytics measures website interactions, not off-site outcomes. A WhatsApp, telephone, or directions click does not prove a conversation, qualified lead, visit, reservation, purchase, or delivery.

The shared public layout installs Google Analytics 4 measurement ID `G-TCW2YWKXRS` using Google's `gtag.js` snippet. It also emits one non-PII page/event context object, while `resources/js/app.js` provides one delegated custom-event layer. Core navigation, contact links, and forms remain usable when JavaScript, storage, or Google Analytics is unavailable.

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

## Provider and privacy

GA4 is installed directly with the supplied measurement ID `G-TCW2YWKXRS`; the former environment-controlled disabled loader has been removed. The custom layer sends only its explicit non-PII event and parameter allowlist through the installed `gtag` function. The repository does not persist analytics events internally.

The production owner remains responsible for an accurate privacy notice, any consent behavior required for the target users and jurisdictions, GA4 data-retention settings, Google Signals/advertising settings, internal-traffic filtering, and URL/query-parameter redaction. If consent gating is later required, implement Google Consent Mode before the initial `config` call rather than restoring a second competing loader.

## Production verification

After deployment:

1. Use GA4 Realtime/DebugView and browser network tools to confirm `G-TCW2YWKXRS` receives page views and each supported event once from every relevant CTA.
2. Confirm the production privacy notice and any required consent implementation accurately describe the active Google tag.
3. Confirm rejected forms never emit `enquiry_submitted`, accepted redirects emit it once, and no PII or full campaign URL appears in payloads.
4. Test unknown/denied consent, ad blocking, offline navigation, back/forward cache, and refresh behavior. Missing analytics must never block the customer action.
5. Label reports as views, clicks, and accepted enquiries. Do not rename these metrics as conversations, leads qualified by staff, visits, or sales.

Search Console is configured separately and does not require interaction events.
