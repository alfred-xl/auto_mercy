@props([
    'title' => 'Auto Mercy',
    'description' => 'Quality brand-new, foreign-used, and pre-order cars from Auto Mercy in Lagos.',
    'canonical' => url()->current(),
    'image' => asset('images/auto-mercy-hero.webp'),
    'robots' => 'index,follow',
    'structuredData' => null,
    'showFloatingWhatsApp' => true,
    'condensedContactFooter' => false,
])

@php
    $socialLinks = collect((array) config('automercy.social'))->filter(fn ($url) => filled($url));
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#161513">
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
    <meta property="og:image:alt" content="Auto Mercy vehicles in Lagos">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $title }}">
    <meta name="twitter:description" content="{{ $description }}">
    <meta name="twitter:image" content="{{ $image }}">

    @if ($structuredData)
        <script type="application/ld+json">{!! json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>
    @endif

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen overflow-x-hidden bg-surface-primary font-sans text-[0.9375rem] text-text-primary antialiased">
    <a href="#main-content" class="fixed left-4 top-3 z-[100] -translate-y-24 bg-pure-white px-4 py-3 font-semibold text-carbon shadow-overlay transition-transform focus:translate-y-0">Skip to main content</a>

    <header class="sticky top-0 z-50 border-b border-border-default bg-pure-white font-sans shadow-sm">
        <div class="mx-auto flex min-h-24 max-w-site items-center justify-between gap-5 px-gutter">
            <a href="{{ route('home') }}" class="inline-flex shrink-0 items-center" aria-label="Auto Mercy home">
                <img src="{{ asset('logo.png') }}" width="500" height="500" alt="Auto Mercy of God Nigeria Limited" class="h-[4.5rem] w-[4.5rem] object-contain lg:h-20 lg:w-20">
            </a>

            <nav class="hidden items-center gap-7 lg:flex" aria-label="Primary navigation">
                <a class="public-nav-link" href="{{ route('home') }}" @if (request()->routeIs('home')) aria-current="page" @endif>Home</a>
                <a class="public-nav-link" href="{{ route('cars.index') }}" @if (request()->routeIs('cars.*')) aria-current="page" @endif>Inventory</a>
                <a class="public-nav-link" href="{{ route('services') }}" @if (request()->routeIs('services')) aria-current="page" @endif>Services</a>
                <a class="public-nav-link" href="{{ route('home') }}#about">About</a>
            </nav>

            <a href="{{ route('contact') }}" class="btn-primary hidden lg:inline-flex" @if (request()->routeIs('contact')) aria-current="page" @endif>Contact Us</a>

            <button type="button" class="mobile-menu-trigger inline-flex h-11 w-11 items-center justify-center rounded-button border-2 border-border-strong text-carbon transition-colors hover:border-mercy-red hover:text-mercy-red lg:hidden" aria-expanded="false" aria-controls="mobile-menu" data-mobile-menu-trigger>
                <span class="sr-only">Open navigation</span>
                <x-heroicon-o-bars-3 class="h-6 w-6" aria-hidden="true" />
            </button>
        </div>

        <div id="mobile-menu" class="absolute inset-x-0 top-full hidden border-y border-border-default bg-pure-white shadow-overlay lg:hidden" data-mobile-menu>
            <nav class="mx-auto grid max-w-site gap-1 px-gutter py-5" aria-label="Mobile navigation">
                <a class="mobile-nav-link" href="{{ route('home') }}" @if (request()->routeIs('home')) aria-current="page" @endif>Home</a>
                <a class="mobile-nav-link" href="{{ route('cars.index') }}" @if (request()->routeIs('cars.*')) aria-current="page" @endif>Inventory</a>
                <a class="mobile-nav-link" href="{{ route('services') }}" @if (request()->routeIs('services')) aria-current="page" @endif>Services</a>
                <a class="mobile-nav-link" href="{{ route('home') }}#about">About</a>
                <a class="mobile-nav-link" href="{{ route('contact') }}" @if (request()->routeIs('contact')) aria-current="page" @endif>Contact Us</a>
            </nav>
        </div>
    </header>

    <main id="main-content" tabindex="-1">
        {{ $slot }}
    </main>

    <footer id="contact" class="relative overflow-hidden border-t border-mercy-red bg-carbon text-pure-white">
        <span class="pointer-events-none absolute -right-32 -top-40 h-96 w-96 rounded-full border border-mercy-red/15" aria-hidden="true"></span>
        <span class="pointer-events-none absolute -right-16 -top-24 h-64 w-64 rounded-full border border-white/5" aria-hidden="true"></span>
        <div class="mx-auto grid max-w-site gap-12 px-gutter py-section-compact md:grid-cols-2 lg:grid-cols-[1.25fr_0.75fr_1fr]">
            <div>
                <div class="flex items-center gap-4">
                    <img src="{{ asset('images/auto-mercy-logo.webp') }}" width="64" height="64" alt="" loading="lazy" class="h-16 w-16 rounded-card bg-pure-white object-contain">
                    <div>
                        <p class="font-sans text-2xl font-semibold">Auto Mercy</p>
                        <p class="mt-1 text-sm text-metallic">Auto Mercy of God Nigeria Limited</p>
                    </div>
                </div>
                <p class="mt-6 max-w-md text-sm leading-7 text-metallic">Quality brand-new, foreign-used, and pre-order cars, direct customer assistance, two Lagos offices, and nationwide delivery.</p>
                <p class="mt-4 text-sm font-semibold">Serving drivers since 2023</p>

                @if ($socialLinks->isNotEmpty())
                    <div class="mt-6 flex items-center gap-3" aria-label="Follow Auto Mercy">
                        @foreach ($socialLinks as $platform => $url)
                            <a href="{{ $url }}" class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-white/20 text-pure-white transition-colors hover:border-pure-white hover:bg-pure-white hover:text-carbon" target="_blank" rel="noopener" aria-label="Auto Mercy on {{ ucfirst($platform) }}">
                                @if ($platform === 'instagram')
                                    <svg class="h-5 w-5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/></svg>
                                @elseif ($platform === 'tiktok')
                                    <svg class="h-5 w-5" aria-hidden="true" viewBox="0 0 24 24" fill="currentColor"><path d="M14.5 3c.4 2.2 1.7 3.6 4 4v3.1a9 9 0 0 1-4-1.2v6.2a5.7 5.7 0 1 1-4.9-5.6v3.2a2.6 2.6 0 1 0 1.8 2.5V3h3.1Z"/></svg>
                                @else
                                    <svg class="h-5 w-5" aria-hidden="true" viewBox="0 0 24 24" fill="currentColor"><path d="M13.7 21v-8h2.7l.4-3h-3.1V8.1c0-.9.3-1.5 1.6-1.5H17V3.9c-.3 0-1.3-.1-2.5-.1-2.5 0-4.2 1.5-4.2 4.4V10H7.5v3h2.8v8h3.4Z"/></svg>
                                @endif
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            <div>
                <h2 class="text-sm font-semibold uppercase tracking-label text-metallic">Navigate</h2>
                <nav class="mt-5 grid gap-3 text-sm" aria-label="Footer navigation">
                    <a class="footer-link" href="{{ route('home') }}">Home</a>
                    <a class="footer-link" href="{{ route('cars.index') }}">Inventory</a>
                    <a class="footer-link" href="{{ route('services') }}">Services</a>
                    <a class="footer-link" href="{{ route('home') }}#about">About Auto Mercy</a>
                </nav>
            </div>

            <div>
                @if ($condensedContactFooter)
                    <h2 class="text-sm font-semibold uppercase tracking-label text-metallic">Contact Auto Mercy</h2>
                    <p class="mt-5 max-w-xs text-sm leading-7 text-metallic">All contact options and office details are available above.</p>
                    <a class="footer-link mt-3 inline-flex text-sm font-semibold" href="#contact-options">Return to contact options</a>
                @else
                    <h2 class="text-sm font-semibold uppercase tracking-label text-metallic">Visit or contact</h2>
                    <div class="mt-5 grid gap-3 text-sm text-metallic">
                        <a class="footer-link" href="{{ config('automercy.business.telephone_url') }}">{{ config('automercy.business.phone_display') }}</a>
                        <a class="footer-link break-all" href="mailto:{{ config('automercy.business.email') }}">{{ config('automercy.business.email') }}</a>
                        <a class="footer-link" href="{{ route('home') }}#locations">View our locations</a>
                    </div>
                @endif
            </div>
        </div>
        <div class="border-t border-white/10">
            <div class="mx-auto flex max-w-site flex-col gap-2 px-gutter pb-28 pt-6 text-xs text-metallic sm:flex-row sm:items-center sm:justify-between sm:pt-7">
                <p>© {{ now()->year }} Auto Mercy of God Nigeria Limited. All rights reserved.</p>
                <p>Cars only · Lagos, Nigeria</p>
            </div>
        </div>
    </footer>

    @if ($showFloatingWhatsApp)
        <a href="{{ config('automercy.business.whatsapp_url') }}?text={{ rawurlencode('Hello Auto Mercy, I would like help finding a car.') }}" class="floating-whatsapp fixed z-40 inline-flex min-h-12 items-center gap-2 whitespace-nowrap rounded-full bg-whatsapp px-4 py-3 text-sm font-semibold text-pure-white shadow-overlay transition-[bottom,opacity,transform,box-shadow] hover:-translate-y-0.5 hover:shadow-card-hover focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-whatsapp sm:px-5" target="_blank" rel="noopener" aria-label="Chat with Auto Mercy on WhatsApp" data-floating-whatsapp>
            <x-icons.whatsapp class="h-5 w-5 shrink-0" />
            <span>Chat with us</span>
        </a>
    @endif

</body>
</html>
