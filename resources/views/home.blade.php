<x-layouts.public-layout title="Cars for Sale in Lagos | Auto Mercy"
    description="Shop brand-new, foreign-used, and pre-order cars from Auto Mercy in Lagos. Browse current inventory, get direct assistance, and arrange nationwide delivery."
    :canonical="route('home')" :structured-data="$structuredData">
    <section
        class="relative isolate min-h-[calc(100svh-5rem)] overflow-hidden bg-carbon text-pure-white lg:min-h-[46rem]">
        <img src="{{ asset('images/auto-mercy-2.jpg') }}" width="736" height="385"
            alt="A lineup of cars available from Auto Mercy" fetchpriority="high"
            class="absolute inset-0 -z-30 h-full w-full scale-105 object-cover object-[58%_center] sm:object-center">
        <div class="absolute inset-0 -z-20 bg-[linear-gradient(90deg,rgba(22,21,19,0.94)_0%,rgba(36,33,31,0.76)_42%,rgba(22,21,19,0.50)_70%,rgba(22,21,19,0.82)_100%)]"
            aria-hidden="true"></div>
        <div class="absolute inset-0 -z-10 bg-[radial-gradient(circle_at_55%_42%,transparent_0%,rgba(22,21,19,0.10)_42%,rgba(22,21,19,0.72)_100%)]"
            aria-hidden="true"></div>

        <div
            class="mx-auto flex min-h-[calc(100svh-5rem)] max-w-site flex-col justify-between px-gutter pb-28 pt-10 sm:pb-32 sm:pt-14 lg:min-h-[46rem] lg:pb-32 lg:pt-16">
            <div class="max-w-[70rem] pt-5 sm:pt-8 lg:pt-10">
                {{-- <p class="flex items-center gap-3 text-xs font-semibold uppercase tracking-[0.14em] text-gold sm:text-sm">
                    <span class="h-px w-10 bg-gold" aria-hidden="true"></span>
                    Quality cars, clear guidance, direct support.
                </p> --}}
                <h1 class="mt-6 font-display text-display font-medium leading-[0.92] tracking-display text-pure-white" data-reveal>
                    <span class="block lg:whitespace-nowrap">Find the right car,</span>
                    <span class="block lg:whitespace-nowrap">with clarity at every step.</span>
                </h1>
                <p class="mt-7 max-w-[36rem] text-base leading-7 text-metallic sm:text-lg sm:leading-8" data-reveal data-reveal-delay="90">Browse quality
                    vehicles and move from enquiry to delivery with clear, direct support.</p>
                <div class="mt-10 flex flex-col gap-4 sm:flex-row sm:items-center" data-reveal data-reveal-delay="180">
                    <a href="{{ route('cars.index') }}" class="btn-primary min-h-13 w-full px-7 sm:w-auto sm:min-w-52"
                        data-floating-whatsapp-avoid>Browse Inventory</a>
                    <a href="{{ route('services') }}"
                        class="btn-ghost-light min-h-13 w-full border-pure-white px-7 sm:w-auto sm:min-w-52"
                        data-floating-whatsapp-avoid>Explore Services</a>
                </div>
            </div>

            <div class="mt-16 grid gap-7 lg:grid-cols-[minmax(0,1fr)_minmax(20rem,25rem)] lg:items-end">
                <div class="flex items-center gap-3 text-gold" data-reveal="fade" data-reveal-delay="270">
                    <span class="h-px w-10 bg-gold" aria-hidden="true"></span>
                    <p class="font-display text-sm italic sm:text-base">With God all things are possible.</p>
                </div>

                {{-- <aside class="rounded-card border border-white/40 bg-pearl p-5 text-carbon shadow-overlay sm:p-6"
                    aria-label="Why customers trust Auto Mercy">
                    <div class="flex items-center justify-between gap-5">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-label text-mercy-red">Trusted locally</p>
                            <p class="mt-2 font-display text-3xl font-semibold tracking-display"><span
                                    class="text-mercy-red">{{ count($locations) }}</span> Lagos locations</p>
                        </div>
                        <span
                            class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-gold/15 text-gold">
                            <x-heroicon-o-shield-check class="h-6 w-6" aria-hidden="true" />
                        </span>
                    </div>
                    <div class="mt-5 border-t border-metallic pt-4 text-sm leading-6 text-text-secondary">
                        <p class="font-semibold text-carbon">Serving drivers since 2023</p>
                        <p>Direct guidance from enquiry through collection or nationwide delivery.</p>
                    </div>
                </aside> --}}
            </div>
        </div>
    </section>

    <section class="relative z-10 flow-root bg-pure-white pb-14 sm:pb-16" aria-labelledby="vehicle-search-heading">
        <div class="mx-auto -mt-16 max-w-wide px-gutter sm:-mt-20">
            <div
                class="relative z-20 rounded-[1.5rem] bg-pure-white px-5 py-8 shadow-overlay sm:px-8 sm:py-10 lg:px-14 lg:py-12" data-reveal>
                <div>
                    <p class="text-xs font-bold uppercase tracking-label text-mercy-red sm:text-sm">Find your next car
                    </p>
                    <h2 id="vehicle-search-heading" class="mt-3 font-display font-medium text-h1 text-carbon">Search our
                        inventory</h2>
                </div>

                <form action="{{ route('cars.index') }}" method="GET"
                    class="mt-9 grid gap-5 md:grid-cols-2 lg:grid-cols-[1fr_1fr_1fr_auto] lg:items-end lg:gap-6">
                    <div>
                        <label class="field-label" for="home-make">Make</label>
                        <select id="home-make" name="make" class="field-control h-14 px-5 text-base">
                            <option value="">All makes</option>
                            @foreach ($searchMakes as $make)
                                <option value="{{ $make }}">{{ $make }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="field-label" for="home-model">Model</label>
                        <select id="home-model" name="model" class="field-control h-14 px-5 text-base">
                            <option value="">All models</option>
                            @foreach ($searchModels as $model)
                                <option value="{{ $model->model }}">{{ $model->make }} &mdash; {{ $model->model }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="field-label" for="home-price-max">Maximum price</label>
                        <select id="home-price-max" name="price_max" class="field-control h-14 px-5 text-base">
                            <option value="">Any price</option>
                            @foreach ([10_000_000, 15_000_000, 20_000_000, 30_000_000, 50_000_000, 75_000_000, 100_000_000] as $price)
                                <option value="{{ $price }}">Up to &#8358;{{ number_format($price) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit"
                        class="btn-primary min-h-14 w-full px-9 text-base md:col-span-2 lg:col-span-1 lg:w-auto"
                        data-floating-whatsapp-avoid>Search cars</button>
                </form>

                <div class="mt-10 grid border-t border-border-default pt-8 sm:grid-cols-3 sm:divide-x sm:divide-border-default"
                    aria-label="Why choose Auto Mercy">
                    @foreach ([['CAC registered', $business['cac_number']], ['Serving drivers', 'Since ' . $business['operating_since']], ['Lagos presence', count($locations) . ' locations']] as [$label, $value])
                        <div class="px-3 py-4 text-center first:pt-0 last:pb-0 sm:py-1">
                            <p class="text-xs font-semibold uppercase tracking-label text-text-secondary">
                                {{ $label }}</p>
                            <p class="mt-2 font-display text-2xl font-medium tracking-display text-carbon sm:text-3xl">
                                {{ $value }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="brand-marquee mt-10 overflow-hidden font-display text-xl text-slate/50 sm:text-2xl"
                aria-label="Popular vehicle makes">
                <div class="brand-marquee-track flex w-max items-center">
                    @foreach ([false, true] as $duplicate)
                        <div class="brand-marquee-group flex shrink-0 items-center"
                            @if ($duplicate) aria-hidden="true" @endif>
                            @foreach (['Toyota', 'Honda', 'Lexus', 'Mercedes-Benz', 'BMW', 'Ford', 'Nissan'] as $make)
                                <a href="{{ route('cars.index', ['make' => $make]) }}"
                                    @if ($duplicate) tabindex="-1" @endif
                                    class="shrink-0 transition-colors hover:text-mercy-red">{{ $make }}</a>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <section id="about" class="bg-pearl py-section" aria-labelledby="about-heading">
        <div
            class="mx-auto grid max-w-wide items-center gap-10 px-gutter lg:grid-cols-[minmax(0,1fr)_minmax(0,1fr)] lg:gap-16 xl:gap-24">
            <div class="overflow-hidden rounded-[1.25rem]" data-reveal="left">
                <img src="{{ asset('images/auto-mercy-hero.webp') }}" width="1792" height="1024"
                    alt="Sedan and SUV parked at Auto Mercy" loading="lazy"
                    class="aspect-[3/2] h-full w-full object-cover object-center">
            </div>

            <div class="max-w-2xl" data-reveal="right">
                <p class="text-sm font-bold uppercase tracking-label text-mercy-red">About Auto Mercy</p>
                <h2 id="about-heading"
                    class="mt-5 font-display text-[clamp(2.35rem,4vw,3.5rem)] font-medium leading-[1.05] tracking-heading text-carbon">
                    A clearer, more personal way to buy your next car.
                </h2>
                <p class="mt-8 text-base leading-8 text-text-secondary sm:text-lg sm:leading-9">
                    Auto Mercy of God Nigeria Limited offers brand-new, foreign-used, and pre-order vehicles in Lagos.
                    We keep the experience direct: see what is available, speak with our team, inspect before deciding,
                    and arrange collection or delivery.
                </p>
                <p class="mt-6 text-base leading-8 text-text-secondary sm:text-lg">
                    Serving drivers since {{ $business['operating_since'] }} with straightforward support at every
                    stage.
                </p>
                <div class="mt-9 flex flex-col gap-3 sm:flex-row sm:flex-wrap">
                    <a href="{{ route('cars.index') }}" class="btn-primary min-h-12 px-7 sm:min-w-48">See available
                        cars</a>
                    <a href="{{ route('services') }}" class="btn-secondary min-h-12 px-7 sm:min-w-40">How we help</a>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-pearl py-section" aria-labelledby="latest-cars-heading">
        <div class="mx-auto max-w-site px-gutter">
            <div class="flex flex-col gap-5 md:flex-row md:items-end md:justify-between" data-reveal>
                <div>
                    <p class="text-sm font-semibold uppercase tracking-label text-text-secondary">Fresh inventory</p>
                    <h2 id="latest-cars-heading" class="mt-3 font-display font-medium text-h1">Latest available cars
                    </h2>
                    <p class="mt-3 max-w-2xl text-base text-text-secondary sm:text-lg">Recently added vehicles that are
                        ready for you to
                        explore.</p>
                </div>
                <a href="{{ route('cars.index') }}" class="text-link hidden lg:mb-1 lg:inline-flex">
                    View all inventory
                    <svg class="h-4 w-4" aria-hidden="true" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                    </svg>
                </a>
            </div>
            <a href="{{ route('cars.index') }}" class="text-link mt-7 lg:hidden">
                View all inventory
                <svg class="h-4 w-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                </svg>
            </a>
            @if ($latestCars->isNotEmpty())
                <div class="latest-cars-carousel swiper mt-10" data-latest-cars-carousel
                    aria-label="Latest available cars">
                    <div class="swiper-wrapper" data-reveal-group>
                        @foreach ($latestCars as $car)
                            <div class="swiper-slide" data-reveal><x-vehicle-card :car="$car" /></div>
                        @endforeach
                    </div>
                    <div class="latest-cars-controls mt-7 flex items-center justify-between gap-5 lg:hidden">
                        <div class="latest-cars-pagination swiper-pagination" data-latest-cars-pagination></div>
                        <div class="hidden shrink-0 items-center gap-2 sm:flex">
                            <button type="button" class="latest-cars-navigation" data-latest-cars-previous
                                aria-label="Previous vehicle"><svg viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m15 18-6-6 6-6" />
                                </svg></button>
                            <button type="button" class="latest-cars-navigation" data-latest-cars-next
                                aria-label="Next vehicle"><svg viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m9 6 6 6-6 6" />
                                </svg></button>
                        </div>
                    </div>
                </div>
            @else
                <div class="mt-10 rounded-card bg-pure-white px-6 py-14 text-center shadow-card" data-reveal>
                    <img src="{{ asset('images/auto-mercy-logo.webp') }}" width="80" height="80"
                        alt="" loading="lazy"
                        class="mx-auto h-20 w-20 rounded-card object-contain opacity-70">
                    <h3 class="mt-5 text-2xl font-medium">New arrivals are being prepared</h3>
                    <p class="mx-auto mt-3 max-w-xl text-text-secondary">Our online inventory is being updated. Call or
                        message us for the vehicles currently available at our Lagos locations.</p>
                    <div class="mt-6 flex flex-wrap justify-center gap-3"><a href="{{ $business['telephone_url'] }}"
                            class="btn-secondary">Call {{ $business['phone_display'] }}</a><a
                            href="{{ $whatsappUrl }}" class="btn-primary" target="_blank" rel="noopener">Ask on
                            WhatsApp</a></div>
                </div>
            @endif
        </div>
    </section>

    <section class="bg-pure-white py-section" aria-labelledby="support-heading">
        <div class="mx-auto max-w-site px-gutter">
            <div class="flex flex-col gap-5 md:flex-row md:items-end md:justify-between" data-reveal>
                <div class="max-w-2xl">
                    <p class="text-sm font-semibold uppercase tracking-label text-text-secondary">More than inventory
                    </p>
                    <h2 id="support-heading" class="mt-3 font-display text-h1 font-medium">Practical support from
                        choice to
                        delivery</h2>
                </div>
                <a href="{{ route('services') }}" class="text-link md:mb-1">Explore all services
                    <x-heroicon-o-arrow-right class="h-4 w-4" aria-hidden="true" /></a>
            </div>
            <div class="mt-12 grid gap-6 md:grid-cols-3" data-reveal-group>
                <article class="rounded-card border border-border-default bg-pure-white p-7 sm:p-8" data-reveal>
                    <p class="text-xs font-bold uppercase tracking-label text-mercy-red">01 &middot; Choose</p>
                    <h3 class="mt-5 font-display text-2xl font-medium">Quality available cars</h3>
                    <p class="mt-4 text-base leading-7 text-text-secondary">Browse current vehicles with the key
                        details needed
                        to make a confident shortlist.</p>
                </article>
                <article class="rounded-card border border-border-default bg-pure-white p-7 sm:p-8" data-reveal>
                    <p class="text-xs font-bold uppercase tracking-label text-mercy-red">02 &middot; Inspect</p>
                    <h3 class="mt-5 font-display text-2xl font-medium">Personal buying support</h3>
                    <p class="mt-4 text-base leading-7 text-text-secondary">Speak with a real person, ask questions,
                        and arrange
                        a viewing at the relevant Lagos location.</p>
                </article>
                <article class="rounded-card border border-border-default bg-pure-white p-7 sm:p-8" data-reveal>
                    <p class="text-xs font-bold uppercase tracking-label text-mercy-red">03 &middot; Receive</p>
                    <h3 class="mt-5 font-display text-2xl font-medium">Nationwide delivery</h3>
                    <p class="mt-4 text-base leading-7 text-text-secondary">Collect in Lagos or discuss suitable
                        delivery
                        arrangements for destinations across Nigeria.</p>
                </article>
            </div>
        </div>
    </section>

    <section class="bg-carbon py-section text-pure-white" aria-labelledby="testimonials-heading">
        <div class="mx-auto max-w-site px-gutter">
            <div class="max-w-3xl" data-reveal>
                <p class="text-sm font-semibold uppercase tracking-label text-metallic">What drivers say</p>
                <h2 id="testimonials-heading" class="mt-3 font-display text-h1 font-medium text-pure-white">Trusted by
                    buyers
                    across Lagos</h2>
                <p class="mt-3 text-base text-metallic sm:text-lg">A snapshot of the straightforward experience
                    customers value at Auto Mercy.</p>
            </div>

            {{-- Temporary testimonial copy approved for the initial design; replace with verified customer submissions. --}}
            <div class="mt-12 grid gap-6 md:grid-cols-3" data-reveal-group>
                @foreach ([['Straightforward from the first call to pickup — no pressure, no surprises.', 'Chidinma O.', 'Iju Road, Lagos'], ['They answered every question clearly and helped me compare the right options.', 'Tunde A.', 'Bamboo Plaza, Lagos'], ['The delivery conversation was simple, direct, and everything was explained upfront.', 'Grace E.', 'Delivered outside Lagos']] as [$quote, $name, $location])
                    <figure class="flex min-h-64 flex-col rounded-card border border-white/10 bg-graphite p-7 sm:p-8" data-reveal>
                        <div class="flex gap-1 text-lg tracking-wider text-gold" aria-label="Five out of five stars">
                            <span aria-hidden="true">&#9733;&#9733;&#9733;&#9733;&#9733;</span>
                        </div>
                        <blockquote class="mt-6 flex-1 font-display text-xl italic leading-8 text-pure-white">
                            &ldquo;{{ $quote }}&rdquo;</blockquote>
                        <figcaption class="mt-7">
                            <p class="font-semibold text-pure-white">{{ $name }}</p>
                            <p class="mt-1 text-sm text-metallic">{{ $location }}</p>
                        </figcaption>
                    </figure>
                @endforeach
            </div>
        </div>
    </section>

    <section id="locations" class="bg-pure-white py-section" aria-labelledby="locations-heading">
        <div class="mx-auto max-w-site px-gutter">
            <div class="max-w-3xl" data-reveal>
                <p class="text-sm font-semibold uppercase tracking-label text-mercy-red">Visit Auto Mercy</p>
                <h2 id="locations-heading" class="mt-3 font-display text-h1 font-medium">Our {{ count($locations) }}
                    Lagos
                    locations</h2>
                <p class="mt-3 max-w-2xl text-base leading-7 text-text-secondary sm:text-lg">Call before visiting so
                    our team can confirm the vehicle
                    and its location.</p>
                <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:flex-wrap">
                    <a href="{{ $business['telephone_url'] }}" class="btn-secondary px-7">
                        <svg class="h-4 w-4" aria-hidden="true" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path
                                d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.79 19.79 0 0 1 2.12 4.18 2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.68 2.8a2 2 0 0 1-.45 2.11L8.07 9.9a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.9.32 1.84.55 2.8.68A2 2 0 0 1 22 16.92Z" />
                        </svg>
                        Call {{ $business['phone_display'] }}
                    </a>
                    <a href="{{ $whatsappUrl }}" class="btn-primary px-7" target="_blank" rel="noopener">
                        <svg class="h-4 w-4" aria-hidden="true" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4Z" />
                        </svg>
                        WhatsApp
                    </a>
                </div>
            </div>
            <div class="mt-12 grid gap-6 lg:grid-cols-2" data-reveal-group>
                @foreach ($locations as $location)
                    <article class="rounded-card border border-border-default bg-pure-white p-7 sm:p-8" data-reveal>
                        <p class="flex items-center gap-2 text-xs font-bold uppercase tracking-label text-mercy-red">
                            <svg class="h-4 w-4 text-mercy-red" aria-hidden="true" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="M20 10c0 5-8 12-8 12S4 15 4 10a8 8 0 1 1 16 0Z" />
                                <circle cx="12" cy="10" r="2.5" />
                            </svg>
                            Location
                        </p>
                        <h3 class="mt-4 font-display text-2xl font-medium">{{ $location['name'] }}</h3>
                        <address class="mt-3 max-w-xl not-italic text-base leading-7 text-text-secondary">
                            {{ $location['address'] }}</address>
                        <p class="mt-5 flex items-center gap-2 text-sm font-semibold text-carbon">
                            <svg class="h-4 w-4 shrink-0 text-mercy-red" aria-hidden="true" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                                stroke-linejoin="round">
                                <circle cx="12" cy="12" r="9" />
                                <path d="M12 7v5l3 2" />
                            </svg>
                            {{ $business['opening_hours_display'] }}
                        </p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="border-t border-border-default bg-pure-white py-section" aria-labelledby="faq-heading">
        <div class="mx-auto max-w-site px-gutter">
            <div class="max-w-3xl" data-reveal>
                <p class="text-xs font-bold uppercase tracking-label text-mercy-red sm:text-sm">Common questions</p>
                <h2 id="faq-heading" class="mt-3 font-display text-h1 font-medium">Before you reach out</h2>

                <div class="mt-10 divide-y divide-border-default border-b border-border-default">
                    @foreach ([['Can I inspect a car before deciding?', 'Yes. Contact our team to confirm where the vehicle is located and arrange a suitable time to inspect it before making your decision.'], ['What kinds of vehicles does Auto Mercy offer?', 'Our inventory can include brand-new, foreign-used, and pre-order vehicles. Check each listing for its current category and availability.'], ['How does nationwide delivery work?', 'Delivery arrangements depend on the vehicle and destination. Speak with our team for timing, cost, and collection details before confirming your purchase.'], ['Do you offer financing or trade-ins?', 'Availability can change, so please contact the team to discuss the current financing or trade-in options for your preferred vehicle.'], ['How do I know which Lagos location has a vehicle?', 'Call or message us before visiting. We will confirm whether the vehicle is at Iju Road or Bamboo Plaza and help arrange your visit.']] as $index => [$question, $answer])
                        <details class="group py-5" @if ($index === 0) open @endif>
                            <summary
                                class="flex min-h-11 cursor-pointer list-none items-center justify-between gap-6 font-display text-lg font-semibold text-carbon marker:content-none">
                                <span>{{ $question }}</span>
                                <span class="text-xl font-medium text-mercy-red group-open:hidden"
                                    aria-hidden="true">+</span>
                                <span class="hidden text-xl font-medium text-mercy-red group-open:inline"
                                    aria-hidden="true">&minus;</span>
                            </summary>
                            <p class="max-w-2xl pb-2 pr-10 text-sm leading-7 text-text-secondary sm:text-base">
                                {{ $answer }}</p>
                        </details>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <section class="border-y border-border-default bg-pure-white py-12 text-carbon sm:py-14"
        aria-labelledby="closing-cta-heading">
        <div
            class="mx-auto flex max-w-site flex-col gap-9 px-gutter md:flex-row md:items-center md:justify-between md:gap-12" data-reveal>
            <div class="max-w-3xl">
                <p class="text-xs font-semibold uppercase tracking-label text-mercy-red">Delivery available nationwide
                </p>
                <h2 id="closing-cta-heading" class="mt-4 font-display text-h2 font-medium">Let us help you find a car
                    that fits.
                </h2>
                <p class="mt-3 max-w-2xl leading-7 text-text-secondary">Browse what is available today or call our team
                    for direct assistance.</p>
            </div>
            <div class="flex shrink-0 flex-col gap-3 sm:flex-row">
                <a href="{{ route('cars.index') }}" class="btn-primary px-7 sm:min-w-36"
                    data-floating-whatsapp-avoid>Browse cars</a>
                <a href="{{ $business['telephone_url'] }}" class="btn-secondary px-7 sm:min-w-44"
                    data-floating-whatsapp-avoid>Call {{ $business['phone_display'] }}</a>
            </div>
        </div>
    </section>
</x-layouts.public-layout>
