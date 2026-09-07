<x-layouts.public-layout
    title="Cars for Sale in Lagos | Auto Mercy"
    description="Shop brand-new, foreign-used, and pre-order cars from Auto Mercy in Lagos. Browse current inventory, get direct assistance, and arrange nationwide delivery."
    :canonical="route('home')"
    :structured-data="$structuredData"
>
    <section class="relative isolate min-h-[40rem] overflow-hidden bg-carbon text-pure-white">
        <img src="{{ asset('images/auto-mercy-2.jpg') }}" width="736" height="385" alt="A lineup of cars available from Auto Mercy" fetchpriority="high" class="absolute inset-0 -z-20 h-full w-full scale-105 object-cover object-center">
        <div class="absolute inset-0 -z-10 bg-[linear-gradient(90deg,rgba(22,21,19,0.65)_0%,rgba(22,21,19,0.60)_52%,rgba(22,21,19,0.56)_100%)]"></div>

        <div class="mx-auto flex min-h-[40rem] max-w-site items-center px-gutter py-20 sm:py-24 lg:py-28">
            <div class="max-w-[46rem]">
                <p class="text-sm font-medium text-white/75">Quality cars, clear guidance, direct support.</p>
                <h1 class="mt-5 max-w-[44rem] font-display text-display font-semibold tracking-display text-pure-white">Find the right car, with clarity at every step.</h1>
                <p class="mt-6 max-w-[40rem] text-base leading-7 text-white/75 sm:text-body-lg sm:leading-8">Browse quality vehicles, speak directly with our team, and move from enquiry to collection or delivery with confidence.</p>
                <div class="mt-9 flex flex-col gap-3 sm:flex-row sm:items-center">
                    <a href="{{ route('cars.index') }}" class="btn-primary w-full sm:w-auto sm:min-w-48" data-floating-whatsapp-avoid>Browse inventory</a>
                    <a href="{{ route('services') }}" class="btn-ghost-light w-full sm:w-auto sm:min-w-48" data-floating-whatsapp-avoid>Explore services</a>
                </div>
                <div class="mt-12 flex items-center gap-3 text-white/55 sm:mt-14">
                    <span class="h-px w-8 bg-gold" aria-hidden="true"></span>
                    <p class="text-xs font-medium">With God all things are possible.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="relative z-10 mx-auto -mt-8 max-w-site px-gutter" aria-labelledby="vehicle-search-heading">
        <div class="rounded-card border-t-2 border-gold bg-pure-white p-5 shadow-overlay md:p-7">
            <div class="mb-6">
                <p class="text-xs font-semibold uppercase tracking-label text-mercy-red">Find your next car</p>
                <h2 id="vehicle-search-heading" class="mt-2 font-display text-3xl font-semibold text-carbon">Search our inventory</h2>
            </div>
            <form action="{{ route('cars.index') }}" method="GET" class="grid gap-4 md:grid-cols-2 lg:grid-cols-[1fr_1fr_1fr_auto] lg:items-end">
                <div><label class="field-label" for="home-make">Make</label><select id="home-make" name="make" class="field-control"><option value="">All makes</option>@foreach ($searchMakes as $make)<option value="{{ $make }}">{{ $make }}</option>@endforeach</select></div>
                <div><label class="field-label" for="home-model">Model</label><select id="home-model" name="model" class="field-control"><option value="">All models</option>@foreach ($searchModels as $model)<option value="{{ $model->model }}">{{ $model->make }} — {{ $model->model }}</option>@endforeach</select></div>
                <div><label class="field-label" for="home-price-max">Maximum price</label><select id="home-price-max" name="price_max" class="field-control"><option value="">Any price</option>@foreach ([10_000_000, 15_000_000, 20_000_000, 30_000_000, 50_000_000, 75_000_000, 100_000_000] as $price)<option value="{{ $price }}">Up to ₦{{ number_format($price) }}</option>@endforeach</select></div>
                <button type="submit" class="btn-primary h-12 w-full px-7 lg:w-auto" data-floating-whatsapp-avoid>Search cars</button>
            </form>
        </div>
    </section>

    <section class="bg-pure-white py-8" aria-label="Why choose Auto Mercy">
        <div class="mx-auto grid max-w-site gap-5 px-gutter sm:grid-cols-3 sm:divide-x sm:divide-border-default">
            @foreach ([['CAC registered', '7328497'], ['Serving drivers', 'Since 2023'], ['Lagos presence', '2 locations']] as [$label, $value])
                <div class="px-4 py-3 text-center"><p class="text-xs font-semibold uppercase tracking-label text-text-secondary">{{ $label }}</p><p class="mt-2 text-xl font-semibold text-carbon">{{ $value }}</p></div>
            @endforeach
        </div>
    </section>

    <section class="bg-surface-secondary py-section" aria-labelledby="latest-cars-heading">
        <div class="mx-auto max-w-site px-gutter">
            <div class="flex flex-col gap-5 md:flex-row md:items-end md:justify-between">
                <div><p class="text-sm font-semibold uppercase tracking-label text-mercy-red">Fresh inventory</p><h2 id="latest-cars-heading" class="mt-3 font-display text-h2">Latest available cars</h2><p class="mt-4 max-w-2xl text-text-secondary">Recently added vehicles that are ready for you to explore.</p></div>
                <a href="{{ route('cars.index') }}" class="text-link hidden lg:mb-1 lg:inline-flex">
                    View all inventory
                    <svg class="h-4 w-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                    </svg>
                </a>
            </div>
            @if ($latestCars->isNotEmpty())
                <div class="latest-cars-carousel swiper mt-10" data-latest-cars-carousel aria-label="Latest available cars">
                    <div class="swiper-wrapper">
                        @foreach ($latestCars as $car)
                            <div class="swiper-slide"><x-vehicle-card :car="$car" /></div>
                        @endforeach
                    </div>
                    <div class="latest-cars-controls mt-7 flex items-center justify-between gap-5 lg:hidden">
                        <div class="latest-cars-pagination swiper-pagination" data-latest-cars-pagination></div>
                        <div class="hidden shrink-0 items-center gap-2 sm:flex">
                            <button type="button" class="latest-cars-navigation" data-latest-cars-previous aria-label="Previous vehicle"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m15 18-6-6 6-6"/></svg></button>
                            <button type="button" class="latest-cars-navigation" data-latest-cars-next aria-label="Next vehicle"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m9 6 6 6-6 6"/></svg></button>
                        </div>
                    </div>
                </div>
                <a href="{{ route('cars.index') }}" class="text-link mt-7 lg:hidden">
                    View all inventory
                    <svg class="h-4 w-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                    </svg>
                </a>
            @else
                <div class="mt-10 rounded-card bg-pure-white px-6 py-14 text-center shadow-card">
                    <img src="{{ asset('images/auto-mercy-logo.webp') }}" width="80" height="80" alt="" loading="lazy" class="mx-auto h-20 w-20 rounded-card object-contain opacity-70">
                    <h3 class="mt-5 text-2xl font-semibold">New arrivals are being prepared</h3>
                    <p class="mx-auto mt-3 max-w-xl text-text-secondary">Our online inventory is being updated. Call or message us for the vehicles currently available at our Lagos locations.</p>
                    <div class="mt-6 flex flex-wrap justify-center gap-3"><a href="{{ $business['telephone_url'] }}" class="btn-secondary">Call {{ $business['phone_display'] }}</a><a href="{{ $whatsappUrl }}" class="btn-primary" target="_blank" rel="noopener">Ask on WhatsApp</a></div>
                </div>
            @endif
        </div>
    </section>

    <section class="bg-pure-white py-section" aria-labelledby="support-heading">
        <div class="mx-auto max-w-site px-gutter">
            <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                <div class="max-w-2xl"><p class="text-sm font-semibold uppercase tracking-label text-mercy-red">More than inventory</p><h2 id="support-heading" class="mt-3 font-display text-h2">Practical support from choice to delivery</h2></div>
                <a href="{{ route('services') }}" class="text-sm font-semibold text-mercy-red underline decoration-mercy-red/30 underline-offset-4 hover:decoration-mercy-red">Explore all services</a>
            </div>
            <div class="mt-10 grid gap-5 md:grid-cols-3">
                <article class="rounded-card bg-pearl p-7"><p class="text-xs font-semibold uppercase tracking-label text-mercy-red">01 · Choose</p><h3 class="mt-5 text-xl font-semibold">Quality available cars</h3><p class="mt-3 leading-7 text-text-secondary">Browse current vehicles with the key details needed to make a confident shortlist.</p></article>
                <article class="rounded-card bg-pearl p-7"><p class="text-xs font-semibold uppercase tracking-label text-mercy-red">02 · Inspect</p><h3 class="mt-5 text-xl font-semibold">Personal buying support</h3><p class="mt-3 leading-7 text-text-secondary">Speak with a real person, ask questions, and arrange a viewing at the relevant Lagos location.</p></article>
                <article class="rounded-card bg-pearl p-7"><p class="text-xs font-semibold uppercase tracking-label text-mercy-red">03 · Receive</p><h3 class="mt-5 text-xl font-semibold">Nationwide delivery</h3><p class="mt-3 leading-7 text-text-secondary">Collect in Lagos or discuss suitable delivery arrangements for destinations across Nigeria.</p></article>
            </div>
        </div>
    </section>

    <section id="about" class="bg-surface-secondary py-section">
        <div class="mx-auto grid max-w-site items-center gap-12 px-gutter lg:grid-cols-2 lg:gap-20">
            <img src="{{ asset('images/auto-mercy-hero.webp') }}" width="1792" height="1024" alt="Cars presented by Auto Mercy in Lagos" loading="lazy" class="aspect-brand-photo w-full rounded-card object-cover object-[72%_center]">
            <div><p class="text-sm font-semibold uppercase tracking-label text-mercy-red">About Auto Mercy</p><h2 class="mt-4 max-w-xl font-display text-h2">A clearer, more personal way to buy your next car.</h2><p class="mt-6 text-body-lg leading-8 text-text-secondary">Auto Mercy of God Nigeria Limited offers brand-new, foreign-used, and pre-order vehicles in Lagos. We keep the experience direct: see what is available, speak with our team, inspect before deciding, and arrange collection or delivery.</p><p class="mt-5 text-text-secondary">Serving drivers since 2023 with straightforward support at every stage.</p><div class="mt-8 flex flex-wrap gap-3"><a href="{{ route('cars.index') }}" class="btn-primary">See available cars</a><a href="{{ route('services') }}" class="btn-secondary">How we help</a></div></div>
        </div>
    </section>

    <section id="locations" class="bg-pure-white py-section" aria-labelledby="locations-heading">
        <div class="mx-auto max-w-site px-gutter">
            <div class="max-w-3xl">
                <p class="text-sm font-semibold uppercase tracking-label text-mercy-red">Visit Auto Mercy</p>
                <h2 id="locations-heading" class="mt-3 font-display text-h2">Our two Lagos locations</h2>
                <p class="mt-4 leading-7 text-text-secondary">Call before visiting so our team can confirm the vehicle and its location.</p>
                <div class="mt-7 flex flex-col gap-3 sm:flex-row sm:flex-wrap">
                    <a href="{{ $business['telephone_url'] }}" class="btn-secondary">
                        <svg class="h-4 w-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.79 19.79 0 0 1 2.12 4.18 2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.68 2.8a2 2 0 0 1-.45 2.11L8.07 9.9a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.9.32 1.84.55 2.8.68A2 2 0 0 1 22 16.92Z"/></svg>
                        Call {{ $business['phone_display'] }}
                    </a>
                    <a href="{{ $whatsappUrl }}" class="btn-primary" target="_blank" rel="noopener">
                        <svg class="h-4 w-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4Z"/></svg>
                        WhatsApp
                    </a>
                </div>
            </div>
            <div class="mt-10 grid gap-grid lg:grid-cols-2">
                @foreach ($locations as $location)
                    <article class="rounded-card bg-pearl p-7 sm:p-8">
                        <p class="flex items-center gap-2 text-xs font-semibold uppercase tracking-label text-text-secondary">
                            <svg class="h-4 w-4 text-mercy-red" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 5-8 12-8 12S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/></svg>
                            Location
                        </p>
                        <h3 class="mt-4 text-xl font-semibold">{{ $location['name'] }}</h3>
                        <address class="mt-5 max-w-xl not-italic leading-7 text-text-secondary">{{ $location['address'] }}</address>
                        <p class="mt-5 flex items-center gap-2 text-sm font-semibold text-carbon">
                            <svg class="h-4 w-4 shrink-0 text-mercy-red" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                            {{ $business['opening_hours_display'] }}
                        </p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="bg-pearl py-section text-carbon" aria-labelledby="closing-cta-heading">
        <div class="mx-auto flex max-w-site flex-col gap-9 px-gutter md:flex-row md:items-center md:justify-between md:gap-12">
            <div class="max-w-3xl">
                <p class="text-xs font-semibold uppercase tracking-label text-mercy-red">Delivery available nationwide</p>
                <h2 id="closing-cta-heading" class="mt-4 font-display text-h2">Let us help you find a car that fits.</h2>
                <p class="mt-4 max-w-2xl leading-7 text-text-secondary">Browse what is available today or call our team for direct assistance.</p>
            </div>
            <div class="flex shrink-0 flex-col gap-3 sm:flex-row">
                <a href="{{ route('cars.index') }}" class="btn-primary sm:min-w-36" data-floating-whatsapp-avoid>Browse cars</a>
                <a href="{{ $business['telephone_url'] }}" class="btn-secondary sm:min-w-44" data-floating-whatsapp-avoid>Call {{ $business['phone_display'] }}</a>
            </div>
        </div>
    </section>
</x-layouts.public-layout>
