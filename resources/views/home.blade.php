<x-layouts.public-layout
    title="Foreign-Used Cars in Lagos | Auto Mercy"
    description="Shop quality foreign-used cars from Auto Mercy in Lagos. Visit our Iju Road or Bamboo Plaza car stand, get direct assistance, and arrange nationwide delivery."
    :canonical="route('home')"
    :structured-data="$structuredData"
>
    <section class="relative isolate min-h-[39rem] overflow-hidden bg-carbon text-pure-white">
        <img src="{{ asset('images/auto-mercy-hero.webp') }}" width="1792" height="1024" alt="Premium cars presented at a modern Lagos car stand" fetchpriority="high" class="absolute inset-0 -z-20 h-full w-full object-cover object-[68%_center]">
        <div class="absolute inset-0 -z-10 bg-[linear-gradient(90deg,rgba(17,16,16,0.96)_0%,rgba(17,16,16,0.83)_38%,rgba(17,16,16,0.3)_72%,rgba(17,16,16,0.38)_100%)]"></div>
        <div class="mx-auto flex min-h-[39rem] max-w-site items-center px-gutter py-section">
            <div class="max-w-3xl pb-20 lg:pb-24">
                <p class="mb-5 flex items-center gap-3 text-sm font-bold uppercase tracking-[0.18em] text-metallic"><span class="h-px w-10 bg-mercy-red" aria-hidden="true"></span>Trusted car sales in Lagos</p>
                <h1 class="max-w-3xl font-display text-display text-pure-white">Drive home a car you can trust.</h1>
                <p class="mt-7 max-w-xl text-body-lg text-white/80">Explore carefully presented foreign-used cars, speak directly with our team, and buy with clear terms from either of our two Lagos car stands.</p>
                <div class="mt-9 flex flex-wrap gap-3">
                    <a href="{{ route('cars.index') }}" class="btn-primary">Browse Available Cars</a>
                    <a href="{{ $whatsappUrl }}" class="btn-ghost-light" target="_blank" rel="noopener">Talk to Us on WhatsApp</a>
                </div>
            </div>
        </div>
    </section>

    <section class="relative z-10 mx-auto -mt-20 max-w-site px-gutter" aria-labelledby="vehicle-search-heading">
        <div class="rounded-card border border-border-default bg-pure-white p-5 shadow-overlay md:p-7">
            <div class="mb-5 flex flex-col gap-2 md:flex-row md:items-end md:justify-between">
                <div><p class="text-xs font-bold uppercase tracking-label text-mercy-red">Find your next car</p><h2 id="vehicle-search-heading" class="mt-1 font-display text-3xl text-carbon">Search available vehicles</h2></div>
                <p class="text-sm text-text-secondary">Search by make, model, budget, or car stand.</p>
            </div>
            <form action="{{ route('cars.index') }}" method="GET" class="grid gap-4 md:grid-cols-2 lg:grid-cols-[1fr_1fr_1fr_1fr_auto] lg:items-end">
                <div><label class="field-label" for="home-make">Make</label><select id="home-make" name="make" class="field-control"><option value="">All makes</option>@foreach ($searchMakes as $make)<option value="{{ $make->slug }}">{{ $make->name }}</option>@endforeach</select></div>
                <div><label class="field-label" for="home-model">Model</label><select id="home-model" name="model" class="field-control"><option value="">All models</option>@foreach ($searchModels as $model)<option value="{{ $model->slug }}">{{ $model->make?->name }} — {{ $model->name }}</option>@endforeach</select></div>
                <div><label class="field-label" for="home-price-max">Maximum price</label><select id="home-price-max" name="price_max" class="field-control"><option value="">Any price</option>@foreach ([10_000_000, 15_000_000, 20_000_000, 30_000_000, 50_000_000, 75_000_000, 100_000_000] as $price)<option value="{{ $price }}">Up to ₦{{ number_format($price) }}</option>@endforeach</select></div>
                <div><label class="field-label" for="home-car-stand">Car stand</label><select id="home-car-stand" name="car_stand" class="field-control"><option value="">Either location</option>@foreach ($searchStands as $stand)<option value="{{ $stand->slug }}">{{ $stand->name }}</option>@endforeach</select></div>
                <button type="submit" class="btn-primary h-12 w-full px-7 lg:w-auto">Search Cars</button>
            </form>
        </div>
    </section>

    <section class="border-b border-border-default bg-pure-white" aria-label="Why choose Auto Mercy">
        <div class="mx-auto grid max-w-site grid-cols-2 gap-px bg-border-default md:grid-cols-5">
            @foreach ([['CAC Registered', '7328497'], ['Serving Drivers', 'Since 2023'], ['Lagos Presence', '2 car stands'], ['Vehicle Focus', 'Foreign-used cars'], ['Delivery', 'Nationwide']] as [$label, $value])
                <div class="bg-pure-white px-5 py-6 text-center last:col-span-2 md:last:col-span-1"><p class="text-xs font-bold uppercase tracking-label text-text-secondary">{{ $label }}</p><p class="mt-2 font-display text-xl text-carbon">{{ $value }}</p></div>
            @endforeach
        </div>
    </section>

    <section class="bg-surface-secondary py-section" aria-labelledby="latest-cars-heading">
        <div class="mx-auto max-w-site px-gutter">
            <div class="flex flex-col gap-5 md:flex-row md:items-end md:justify-between">
                <div><p class="text-sm font-bold uppercase tracking-label text-mercy-red">Fresh inventory</p><h2 id="latest-cars-heading" class="mt-3 font-display text-h2">Latest available cars</h2><p class="mt-4 max-w-2xl text-text-secondary">The newest vehicles currently published and available from Auto Mercy.</p></div>
                <a href="{{ route('cars.index') }}" class="btn-secondary hidden lg:mb-1 lg:inline-flex">View All Available Cars</a>
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
                            <button type="button" class="latest-cars-navigation" data-latest-cars-previous aria-label="Previous vehicle">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m15 18-6-6 6-6"/></svg>
                            </button>
                            <button type="button" class="latest-cars-navigation" data-latest-cars-next aria-label="Next vehicle">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m9 6 6 6-6 6"/></svg>
                            </button>
                        </div>
                    </div>
                </div>
                <a href="{{ route('cars.index') }}" class="btn-secondary mt-7 w-full lg:hidden">View All Available Cars</a>
            @else
                <div class="mt-10 rounded-card border border-border-default bg-pure-white px-6 py-14 text-center shadow-card">
                    <img src="{{ asset('images/auto-mercy-logo.webp') }}" width="80" height="80" alt="" loading="lazy" class="mx-auto h-20 w-20 object-contain opacity-70">
                    <h3 class="mt-5 font-display text-3xl">New arrivals are being prepared</h3>
                    <p class="mx-auto mt-3 max-w-xl text-text-secondary">Our online inventory is being updated. Call or message us for the vehicles currently available at our Lagos car stands.</p>
                    <div class="mt-6 flex flex-wrap justify-center gap-3"><a href="{{ $business['telephone_url'] }}" class="btn-secondary">Call {{ $business['phone_display'] }}</a><a href="{{ $whatsappUrl }}" class="btn-primary" target="_blank" rel="noopener">Ask on WhatsApp</a></div>
                </div>
            @endif
        </div>
    </section>

    @if ($availableMakes->isNotEmpty())
        <section class="border-b border-border-default bg-pure-white py-section-compact" aria-labelledby="browse-make-heading">
            <div class="mx-auto max-w-site px-gutter">
                <div class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between"><div><p class="text-sm font-bold uppercase tracking-label text-mercy-red">Browse quickly</p><h2 id="browse-make-heading" class="mt-2 font-display text-h3">Shop by make</h2></div><p class="text-sm text-text-secondary">Only makes with currently available vehicles are shown.</p></div>
                <div class="mt-8 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($availableMakes as $make)
                        <a href="{{ route('cars.index', ['make' => $make->slug]) }}" class="group flex items-center justify-between rounded-card border border-border-default px-5 py-4 transition-colors hover:border-mercy-red hover:bg-pearl"><span class="font-semibold text-carbon group-hover:text-mercy-red">{{ $make->name }}</span><span class="text-sm text-text-secondary">{{ $make->available_cars_count }} {{ Str::plural('car', $make->available_cars_count) }}</span></a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section id="about" class="bg-pure-white py-section">
        <div class="mx-auto grid max-w-site items-center gap-12 px-gutter lg:grid-cols-2 lg:gap-20">
            <div class="relative"><img src="{{ asset('images/auto-mercy-hero.webp') }}" width="1792" height="1024" alt="Foreign-used cars presented by Auto Mercy in Lagos" loading="lazy" class="aspect-brand-photo w-full object-cover object-[72%_center]"><div class="absolute -bottom-6 right-0 max-w-[15rem] bg-mercy-red p-5 text-pure-white sm:right-6"><p class="font-display text-3xl">Cars with clarity.</p><p class="mt-1 text-sm text-white/80">Direct help from first enquiry to delivery.</p></div></div>
            <div><p class="text-sm font-bold uppercase tracking-label text-mercy-red">About Auto Mercy</p><h2 class="mt-4 max-w-xl font-display text-h2">A straightforward way to buy your next car.</h2><p class="mt-6 text-body-lg text-text-secondary">Auto Mercy of God Nigeria Limited sells quality foreign-used cars from two physical locations in Lagos. We help customers inspect available options, understand the buying terms, and arrange delivery across Nigeria.</p><p class="mt-5 text-text-secondary">We have operated since 2023 and are registered with the Corporate Affairs Commission under number 7328497.</p><div class="mt-8 flex flex-wrap gap-3"><a href="{{ route('cars.index') }}" class="btn-primary">See Available Cars</a><a href="#locations" class="btn-secondary">Find a Car Stand</a></div></div>
        </div>
    </section>

    <section id="how-to-buy" class="bg-carbon py-section text-pure-white" aria-labelledby="how-to-buy-heading">
        <div class="mx-auto max-w-site px-gutter">
            <div class="max-w-2xl"><p class="text-sm font-bold uppercase tracking-label text-metallic">Simple buying process</p><h2 id="how-to-buy-heading" class="mt-4 font-display text-h2">How to buy from Auto Mercy</h2><p class="mt-5 text-white/70">Speak with our team at every step, whether you begin online or visit one of our Lagos car stands.</p></div>
            <ol class="mt-12 grid gap-8 md:grid-cols-2 lg:grid-cols-4">
                @foreach ([['Choose a car', 'Browse online or contact us for the latest available options.'], ['Inspect and confirm', 'Visit the relevant car stand and review the vehicle with our team.'], ['Pay or reserve', 'Complete payment, or reserve the vehicle under the terms below.'], ['Collect or deliver', 'Pick up in Lagos or arrange nationwide delivery.']] as $index => [$title, $copy])
                    <li class="border-t border-white/25 pt-5"><span class="font-display text-4xl text-mercy-red">0{{ $index + 1 }}</span><h3 class="mt-5 text-xl font-semibold">{{ $title }}</h3><p class="mt-3 text-sm leading-7 text-white/65">{{ $copy }}</p></li>
                @endforeach
            </ol>
            <div class="mt-12 grid gap-6 border border-white/20 bg-graphite p-6 md:grid-cols-[auto_1fr] md:items-center md:p-8"><div class="flex h-14 w-14 items-center justify-center bg-mercy-red font-display text-2xl" aria-hidden="true">₦</div><div><h3 class="text-xl font-semibold">Reservation terms</h3><p class="mt-2 leading-7 text-white/75">A minimum reservation payment of <strong class="text-pure-white">₦500,000</strong> holds a vehicle for up to <strong class="text-pure-white">14 days</strong>. Reservation payments are non-refundable.</p></div></div>
        </div>
    </section>

    <section id="locations" class="bg-surface-secondary py-section" aria-labelledby="locations-heading">
        <div class="mx-auto max-w-site px-gutter">
            <div class="max-w-2xl"><p class="text-sm font-bold uppercase tracking-label text-mercy-red">Visit Auto Mercy</p><h2 id="locations-heading" class="mt-3 font-display text-h2">Two Lagos car stands</h2><p class="mt-4 text-text-secondary">Open Monday–Saturday, 8:00 AM–6:00 PM. Call before visiting so our team can confirm the vehicle and location.</p></div>
            <div class="mt-10 grid gap-grid lg:grid-cols-2">
                @foreach ($locations as $location)
                    <article class="rounded-card border border-border-default bg-pure-white p-6 shadow-card md:p-8"><p class="text-xs font-bold uppercase tracking-label text-mercy-red">Lagos, Nigeria</p><h3 class="mt-3 font-display text-3xl">{{ $location['name'] }}</h3><address class="mt-5 max-w-xl not-italic leading-7 text-text-secondary">{{ $location['address'] }}</address><p class="mt-4 text-sm font-semibold text-carbon">Monday–Saturday, 8:00 AM–6:00 PM</p><div class="mt-7 flex flex-wrap gap-3"><a href="{{ $business['telephone_url'] }}" class="btn-secondary">Call {{ $business['phone_display'] }}</a><a href="{{ $whatsappUrl }}" class="btn-primary" target="_blank" rel="noopener">WhatsApp</a>@if ($location['map_url'])<a href="{{ $location['map_url'] }}" class="btn-secondary" target="_blank" rel="noopener">Get Directions</a>@endif</div></article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="bg-graphite py-section-compact text-pure-white"><div class="mx-auto flex max-w-site flex-col gap-8 px-gutter md:flex-row md:items-center md:justify-between"><div><p class="text-sm font-bold uppercase tracking-label text-metallic">Beyond Lagos</p><h2 class="mt-3 font-display text-h3">Nationwide vehicle delivery</h2><p class="mt-3 max-w-2xl text-white/65">Found the right car? Speak with our team to discuss delivery to your location anywhere in Nigeria.</p></div><a href="{{ $whatsappUrl }}" class="btn-light shrink-0" target="_blank" rel="noopener">Discuss Delivery</a></div></section>
    <section class="bg-mercy-red py-section-compact text-pure-white"><div class="mx-auto flex max-w-site flex-col gap-7 px-gutter md:flex-row md:items-center md:justify-between"><div><p class="text-sm font-bold uppercase tracking-label text-white/70">Ready when you are</p><h2 class="mt-3 max-w-3xl font-display text-h3">Let us help you find a car that fits.</h2></div><div class="flex flex-wrap gap-3"><a href="{{ route('cars.index') }}" class="btn-light">Browse Cars</a><a href="{{ $business['telephone_url'] }}" class="btn-ghost-light">Call {{ $business['phone_display'] }}</a></div></div></section>
</x-layouts.public-layout>
