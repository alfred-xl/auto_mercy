<x-layouts.public-layout
    title="Vehicle Services in Lagos | Auto Mercy"
    description="Explore Auto Mercy vehicle sales, viewing support, reservation assistance, and nationwide delivery services."
>
    <section class="bg-pure-white py-section-compact sm:py-section">
        <div class="mx-auto grid max-w-site items-center gap-12 px-gutter lg:grid-cols-[1.02fr_0.98fr] lg:gap-20">
            <div>
                <p class="eyebrow text-mercy-red">How we help</p>
                <h1 class="mt-4 max-w-3xl font-display text-page-title font-semibold tracking-display text-carbon sm:text-hero">Practical support from search to delivery.</h1>
                <p class="mt-6 max-w-2xl text-base leading-8 text-text-secondary">Whether you already know the car you want or need a trusted team to guide the process, Auto Mercy keeps every step clear, direct, and personal.</p>
                <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                    <a href="{{ route('cars.index') }}" class="btn-primary">Browse inventory</a>
                    <a href="{{ $whatsappUrl }}" class="btn-secondary" target="_blank" rel="noopener">Talk to our team</a>
                </div>
            </div>

            <div class="relative overflow-hidden rounded-card bg-carbon shadow-card">
                <img src="{{ asset('images/auto-mercy-1.jpg') }}" width="626" height="417" alt="A selection of vehicles available through Auto Mercy" class="aspect-[4/3] w-full object-cover" fetchpriority="high">
                <div class="absolute inset-x-4 bottom-4 flex items-center gap-3 rounded-button border border-white/10 bg-carbon/90 px-4 py-3 text-pure-white shadow-card backdrop-blur-sm sm:left-5 sm:right-auto sm:max-w-sm">
                    <x-heroicon-o-map-pin class="h-5 w-5 shrink-0 text-gold" aria-hidden="true" />
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-label text-metallic">Local presence</p>
                        <p class="mt-1 text-sm font-semibold sm:text-base">Two Lagos locations, one dedicated team.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-surface-secondary py-section" aria-labelledby="services-heading">
        <div class="mx-auto max-w-site px-gutter">
            <div class="grid gap-5 md:grid-cols-[1fr_0.7fr] md:items-end">
                <div class="max-w-2xl">
                <p class="eyebrow text-mercy-red">Our services</p>
                <h2 id="services-heading" class="mt-3 font-display text-section-title font-semibold tracking-display">Support built around the way you buy</h2>
                </div>
                <p class="max-w-xl leading-7 text-text-secondary md:justify-self-end">Straightforward services, honest communication, and a real person available when you need help.</p>
            </div>

            <div class="mt-10 grid gap-grid lg:grid-cols-12">
                <article class="rounded-card bg-carbon p-7 text-pure-white shadow-card sm:p-9 lg:col-span-7">
                    <span class="inline-flex h-12 w-12 items-center justify-center rounded-button border border-white/15 bg-white/5 text-gold">
                        <x-heroicon-o-key class="h-6 w-6" aria-hidden="true" />
                    </span>
                    <p class="mt-8 text-xs font-semibold uppercase tracking-label text-gold">Primary service</p>
                    <h3 class="mt-3 text-2xl font-semibold">Foreign-used vehicle sales</h3>
                    <p class="mt-4 max-w-2xl leading-7 text-white/70">Explore available cars with clear pricing, useful specifications, and photos that help you shortlist with confidence.</p>
                    <p class="mt-7 border-t border-white/10 pt-5 text-sm font-medium text-white/85">Clear pricing · Useful specifications · Detailed vehicle photos</p>
                </article>

                <article class="rounded-card border border-border-default bg-pure-white p-7 shadow-card sm:p-9 lg:col-span-5">
                    <span class="inline-flex h-12 w-12 items-center justify-center rounded-button bg-mercy-red/10 text-mercy-red">
                        <x-heroicon-o-magnifying-glass class="h-6 w-6" aria-hidden="true" />
                    </span>
                    <h3 class="mt-8 text-xl font-semibold">Viewing and selection support</h3>
                    <p class="mt-3 leading-7 text-text-secondary">Tell us what matters to you. We will help narrow the options and arrange a viewing at the appropriate Lagos location.</p>
                </article>

                <article class="rounded-card border border-border-default bg-pure-white p-7 sm:p-8 lg:col-span-6">
                    <span class="inline-flex h-11 w-11 items-center justify-center rounded-button bg-available/10 text-available">
                        <x-heroicon-o-document-check class="h-5 w-5" aria-hidden="true" />
                    </span>
                    <h3 class="mt-5 text-xl font-semibold">Reservation assistance</h3>
                    <p class="mt-3 leading-7 text-text-secondary">When you are ready, our team explains the reservation terms and next steps clearly before you commit.</p>
                </article>

                <article class="rounded-card border border-border-default bg-pure-white p-7 sm:p-8 lg:col-span-6">
                    <span class="inline-flex h-11 w-11 items-center justify-center rounded-button bg-gold/15 text-carbon">
                        <x-heroicon-o-truck class="h-5 w-5" aria-hidden="true" />
                    </span>
                    <h3 class="mt-5 text-xl font-semibold">Nationwide delivery</h3>
                    <p class="mt-3 leading-7 text-text-secondary">Buying outside Lagos? Speak with the team about delivery availability and arrangements for your destination.</p>
                </article>
            </div>
        </div>
    </section>

    <section class="bg-pure-white py-section" aria-labelledby="process-heading">
        <div class="mx-auto max-w-site px-gutter">
            <div class="grid gap-12 lg:grid-cols-[0.72fr_1.28fr] lg:gap-20">
                <div class="max-w-md">
                <p class="eyebrow text-mercy-red">A simpler process</p>
                <h2 id="process-heading" class="mt-3 font-display text-section-title font-semibold tracking-display">Three clear steps to your next car</h2>
                    <p class="mt-5 leading-7 text-text-secondary">A straightforward path from finding the right vehicle to collection or delivery.</p>
                </div>

                <ol class="grid gap-0">
                    <li class="grid grid-cols-[3.25rem_1fr] gap-5 border-b border-border-default pb-7">
                        <span class="inline-flex h-11 w-11 items-center justify-center rounded-full bg-mercy-red text-sm font-semibold text-pure-white">01</span>
                        <div><h3 class="text-xl font-semibold">Choose a vehicle</h3><p class="mt-2 leading-7 text-text-secondary">Browse the inventory or tell us your preferred make, budget, and must-have features.</p></div>
                    </li>
                    <li class="grid grid-cols-[3.25rem_1fr] gap-5 border-b border-border-default py-7">
                        <span class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-mercy-red text-sm font-semibold text-mercy-red">02</span>
                        <div><h3 class="text-xl font-semibold">Talk and inspect</h3><p class="mt-2 leading-7 text-text-secondary">Ask questions, arrange a viewing, and inspect the vehicle before making your decision.</p></div>
                    </li>
                    <li class="grid grid-cols-[3.25rem_1fr] gap-5 pt-7">
                        <span class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-mercy-red text-sm font-semibold text-mercy-red">03</span>
                        <div><h3 class="text-xl font-semibold">Complete and collect</h3><p class="mt-2 leading-7 text-text-secondary">Confirm the terms with our team, complete payment, then collect or arrange delivery.</p></div>
                    </li>
                </ol>
            </div>

            <div class="mt-16 border-y border-border-default py-8">
                <p class="eyebrow text-mercy-red">Why Auto Mercy</p>
                <ul class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach (['Two Lagos locations', 'Direct team assistance', 'Inspection before purchase', 'Nationwide delivery arrangements'] as $trustPoint)
                        <li class="flex items-start gap-3 text-sm font-semibold text-carbon">
                            <x-heroicon-o-check-circle class="mt-0.5 h-5 w-5 shrink-0 text-available" aria-hidden="true" />
                            <span>{{ $trustPoint }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </section>

    <section class="border-b border-white/10 bg-carbon py-section-compact text-pure-white">
        <div class="mx-auto flex max-w-site flex-col items-start justify-between gap-8 px-gutter md:flex-row md:items-center">
            <div>
                <p class="text-xs font-semibold uppercase tracking-label text-gold">Ready when you are</p>
                <h2 class="mt-2 font-display text-section-title font-semibold tracking-display">Start with the cars available today.</h2>
                <p class="mt-3 max-w-2xl leading-7 text-white/65">Browse the current inventory or contact our team when you need personal guidance.</p>
            </div>
            <div class="flex w-full shrink-0 flex-col gap-4 sm:w-auto sm:flex-row sm:items-center">
                <a href="{{ route('cars.index') }}" class="btn-primary">Browse inventory</a>
                <a href="{{ route('contact') }}" class="inline-flex min-h-11 items-center justify-center text-sm font-semibold text-pure-white underline decoration-white/35 decoration-2 underline-offset-4 transition-colors hover:decoration-pure-white">Contact Us</a>
            </div>
        </div>
    </section>
</x-layouts.public-layout>
