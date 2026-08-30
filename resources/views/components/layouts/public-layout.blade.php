@props([
    'title' => 'Auto Mercy',
    'description' => 'Quality foreign-used cars from Auto Mercy in Lagos.',
    'canonical' => url()->current(),
    'image' => asset('images/auto-mercy-hero.webp'),
    'robots' => 'index,follow',
    'structuredData' => null,
])

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#111010">
    <meta name="robots" content="{{ $robots }}">
    <title>{{ $title }}</title>
    <meta name="description" content="{{ $description }}">
    <link rel="canonical" href="{{ $canonical }}">

    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Auto Mercy">
    <meta property="og:title" content="{{ $title }}">
    <meta property="og:description" content="{{ $description }}">
    <meta property="og:url" content="{{ $canonical }}">
    <meta property="og:image" content="{{ $image }}">
    <meta property="og:image:alt" content="Auto Mercy vehicles at a Lagos car stand">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $title }}">
    <meta name="twitter:description" content="{{ $description }}">
    <meta name="twitter:image" content="{{ $image }}">

    @if ($structuredData)
        <script type="application/ld+json">{!! json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>
    @endif

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen overflow-x-hidden bg-surface-primary font-sans text-text-primary antialiased pb-[calc(4.5rem+env(safe-area-inset-bottom))] lg:pb-0">
    <a href="#main-content" class="fixed left-4 top-3 z-[100] -translate-y-24 bg-pure-white px-4 py-3 font-semibold text-carbon shadow-overlay transition-transform focus:translate-y-0">Skip to main content</a>

    <div class="bg-carbon text-pure-white">
        <div class="mx-auto flex min-h-10 max-w-site items-center justify-between gap-4 px-gutter py-2 text-xs font-medium tracking-wide">
            <p class="hidden items-center gap-2 sm:flex">
                <span class="h-1.5 w-1.5 bg-mercy-red" aria-hidden="true"></span>
                CAC Registered: 7328497
            </p>
            <p class="hidden md:block">Monday–Saturday, 8:00 AM–6:00 PM</p>
            <div class="ml-auto flex items-center gap-4">
                <a class="transition-colors hover:text-metallic" href="mailto:{{ config('automercy.business.email') }}">{{ config('automercy.business.email') }}</a>
                <a class="font-semibold text-pure-white transition-colors hover:text-metallic" href="{{ config('automercy.business.telephone_url') }}">{{ config('automercy.business.phone_display') }}</a>
            </div>
        </div>
    </div>

    <header class="relative z-50 border-b border-border-default bg-pure-white">
        <div class="mx-auto flex min-h-20 max-w-site items-center justify-between gap-6 px-gutter">
            <a href="{{ route('home') }}" class="inline-flex shrink-0 items-center" aria-label="Auto Mercy home">
                <img src="{{ asset('logo.png') }}" width="500" height="500" alt="Auto Mercy of God Nigeria Limited" class="h-16 w-16 object-contain">
            </a>

            <nav class="hidden items-center gap-7 lg:flex" aria-label="Primary navigation">
                <a class="public-nav-link" href="{{ route('home') }}">Home</a>
                <a class="public-nav-link" href="{{ route('cars.index') }}">Available Cars</a>
                <a class="public-nav-link" href="{{ route('home') }}#how-to-buy">How to Buy</a>
                <a class="public-nav-link" href="{{ route('home') }}#about">About</a>
                <a class="public-nav-link" href="{{ route('home') }}#locations">Car Stands</a>
                <a class="public-nav-link" href="{{ route('home') }}#contact">Contact</a>
            </nav>

            <div class="hidden lg:block">
                <a href="{{ config('automercy.business.whatsapp_url') }}?text={{ rawurlencode('Hello Auto Mercy, I would like help finding a car.') }}" class="btn-primary" target="_blank" rel="noopener">WhatsApp Us</a>
            </div>

            <button type="button" class="inline-flex h-11 w-11 items-center justify-center border border-border-default text-carbon transition-colors hover:border-carbon lg:hidden" aria-expanded="false" aria-controls="mobile-menu" data-mobile-menu-trigger>
                <span class="sr-only">Open navigation</span>
                <svg class="h-6 w-6" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
            </button>
        </div>

        <div id="mobile-menu" class="absolute inset-x-0 top-full hidden border-y border-border-default bg-pure-white shadow-overlay lg:hidden" data-mobile-menu>
            <nav class="mx-auto grid max-w-site px-gutter py-5" aria-label="Mobile navigation">
                <a class="mobile-nav-link" href="{{ route('home') }}">Home</a>
                <a class="mobile-nav-link" href="{{ route('cars.index') }}">Available Cars</a>
                <a class="mobile-nav-link" href="{{ route('home') }}#how-to-buy">How to Buy</a>
                <a class="mobile-nav-link" href="{{ route('home') }}#about">About</a>
                <a class="mobile-nav-link" href="{{ route('home') }}#locations">Car Stands</a>
                <a class="mobile-nav-link" href="{{ route('home') }}#contact">Contact</a>
            </nav>
        </div>
    </header>

    <main id="main-content" tabindex="-1">
        {{ $slot }}
    </main>

    <footer id="contact" class="bg-carbon text-pure-white">
        <div class="mx-auto grid max-w-site gap-12 px-gutter py-section-compact md:grid-cols-2 lg:grid-cols-[1.25fr_0.75fr_1fr]">
            <div>
                <div class="flex items-center gap-4">
                    <img src="{{ asset('images/auto-mercy-logo.webp') }}" width="64" height="64" alt="" loading="lazy" class="h-16 w-16 bg-pure-white object-contain">
                    <div>
                        <p class="font-display text-3xl">Auto Mercy</p>
                        <p class="mt-1 text-sm text-metallic">Auto Mercy of God Nigeria Limited</p>
                    </div>
                </div>
                <p class="mt-6 max-w-md text-sm leading-7 text-metallic">Quality foreign-used cars, direct customer assistance, two physical Lagos car stands, and nationwide delivery.</p>
                <p class="mt-4 text-sm font-semibold">CAC 7328497 · Since 2023</p>
            </div>

            <div>
                <h2 class="text-sm font-semibold uppercase tracking-label text-metallic">Navigate</h2>
                <nav class="mt-5 grid gap-3 text-sm" aria-label="Footer navigation">
                    <a class="footer-link" href="{{ route('home') }}">Home</a>
                    <a class="footer-link" href="{{ route('cars.index') }}">Available Cars</a>
                    <a class="footer-link" href="{{ route('home') }}#about">About Auto Mercy</a>
                    <a class="footer-link" href="{{ route('home') }}#how-to-buy">How to Buy</a>
                    <a class="footer-link" href="{{ route('home') }}#locations">Car Stands</a>
                </nav>
            </div>

            <div>
                <h2 class="text-sm font-semibold uppercase tracking-label text-metallic">Visit or contact</h2>
                <div class="mt-5 grid gap-5 text-sm text-metallic">
                    @foreach (config('automercy.locations') as $location)
                        <address class="not-italic"><strong class="block text-pure-white">{{ $location['name'] }}</strong>{{ $location['address'] }}</address>
                    @endforeach
                    <p>Monday–Saturday, 8:00 AM–6:00 PM</p>
                    <p class="grid gap-2">
                        <a class="footer-link" href="{{ config('automercy.business.telephone_url') }}">{{ config('automercy.business.phone_display') }}</a>
                        <a class="footer-link" href="{{ config('automercy.business.whatsapp_url') }}" target="_blank" rel="noopener">WhatsApp</a>
                        <a class="footer-link break-all" href="mailto:{{ config('automercy.business.email') }}">{{ config('automercy.business.email') }}</a>
                    </p>
                </div>
            </div>
        </div>
        <div class="border-t border-white/10">
            <div class="mx-auto flex max-w-site flex-col gap-2 px-gutter py-5 text-xs text-metallic sm:flex-row sm:items-center sm:justify-between">
                <p>© {{ now()->year }} Auto Mercy of God Nigeria Limited. All rights reserved.</p>
                <p>Cars only · Lagos, Nigeria</p>
            </div>
        </div>
    </footer>

    <div class="fixed inset-x-0 bottom-0 z-50 grid grid-cols-2 border-t border-white/15 bg-carbon pb-[env(safe-area-inset-bottom)] text-sm font-semibold text-pure-white lg:hidden" aria-label="Quick contact">
        <a href="{{ config('automercy.business.telephone_url') }}" class="flex min-h-16 items-center justify-center gap-2 border-r border-white/15">
            <svg class="h-5 w-5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M5 4h3l2 5-2 1a15 15 0 0 0 6 6l1-2 5 2v3a2 2 0 0 1-2 2C10 21 3 14 3 6a2 2 0 0 1 2-2Z"/></svg>
            Call
        </a>
        <a href="{{ config('automercy.business.whatsapp_url') }}?text={{ rawurlencode('Hello Auto Mercy, I would like help finding a car.') }}" class="flex min-h-16 items-center justify-center gap-2 bg-whatsapp text-carbon" target="_blank" rel="noopener">
            <svg class="h-5 w-5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M20 11.5a8 8 0 0 1-11.8 7L4 20l1.5-4A8 8 0 1 1 20 11.5Z"/><path d="M9 8.5c.5 3 2 4.5 5 5"/></svg>
            WhatsApp
        </a>
    </div>
</body>
</html>
