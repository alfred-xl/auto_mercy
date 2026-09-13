<x-layouts.public-layout
    title="Vehicle Services in Lagos | Auto Mercy"
    description="Explore Auto Mercy vehicle sales, viewing support, reservation assistance, and nationwide delivery services."
>
    <section class="bg-pearl py-section-compact sm:py-section">
        <div class="mx-auto grid max-w-wide items-center gap-12 px-gutter lg:grid-cols-[0.95fr_1.05fr] lg:gap-20 xl:gap-28">
            <div class="max-w-2xl" data-reveal="left">
                <p class="text-sm font-semibold uppercase tracking-label text-text-secondary">How we help</p>
                <h1 class="mt-7 max-w-xl font-display text-[clamp(2.75rem,5vw,4rem)] font-medium leading-[1.02] tracking-display text-carbon">
                    Practical support from search to delivery.
                </h1>
                <p class="mt-8 max-w-xl text-base leading-8 text-text-secondary sm:text-lg sm:leading-9">
                    Whether you already know the car you want or need a trusted team to guide the process, Auto Mercy
                    keeps every step clear, direct, and personal.
                </p>
                <div class="mt-9 flex flex-col gap-3 sm:flex-row sm:flex-wrap">
                    <a href="{{ route('cars.index') }}" class="btn-primary min-h-12 rounded-full px-8 sm:min-w-48">Browse inventory</a>
                    <a href="{{ $whatsappUrl }}" class="btn-secondary min-h-12 rounded-full px-8 sm:min-w-44" target="_blank" rel="noopener">Talk to our team</a>
                </div>
            </div>

            <div class="relative overflow-hidden rounded-[1.5rem] bg-carbon shadow-overlay" data-reveal="right">
                <img src="{{ asset('images/auto-mercy-1.jpg') }}" width="626" height="417"
                    alt="A row of vehicles at Auto Mercy" class="aspect-[4/3] w-full object-cover" fetchpriority="high">
                <div class="absolute inset-x-4 bottom-4 flex items-center gap-4 rounded-xl bg-carbon/95 px-5 py-4 text-pure-white shadow-card backdrop-blur-sm sm:inset-x-6 sm:bottom-6 sm:px-6">
                    <x-heroicon-o-map-pin class="h-5 w-5 shrink-0 text-mercy-red" aria-hidden="true" />
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-label text-metallic">Local presence</p>
                        <p class="mt-1 text-sm font-medium sm:text-base">Two Lagos locations, one dedicated team.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-pure-white py-section" aria-labelledby="services-heading">
        <div class="mx-auto max-w-site px-gutter">
            <div class="grid gap-6 md:grid-cols-[1fr_0.7fr] md:items-end" data-reveal>
                <div class="max-w-2xl">
                    <p class="text-xs font-semibold uppercase tracking-label text-text-secondary">Our services</p>
                    <h2 id="services-heading" class="mt-5 max-w-xl font-display text-h1 font-medium tracking-display">Support built around the way you buy</h2>
                </div>
                <p class="max-w-xl leading-7 text-text-secondary md:justify-self-end">Straightforward services, honest communication, and a real person available when you need help.</p>
            </div>

            <div class="mt-12 grid gap-5 lg:grid-cols-2" data-reveal-group>
                <article class="flex min-h-[24rem] flex-col rounded-card bg-carbon p-7 text-pure-white sm:p-9 lg:row-span-2" data-reveal>
                    <span class="inline-flex h-12 w-12 items-center justify-center rounded-button border border-white/15 bg-white/5 text-gold">
                        <x-heroicon-o-key class="h-6 w-6" aria-hidden="true" />
                    </span>
                    <div class="mt-7">
                        <p class="text-xs font-semibold uppercase tracking-label text-gold">Primary service</p>
                        <h3 class="mt-3 font-display text-2xl font-medium">Foreign-used vehicle sales</h3>
                        <p class="mt-4 max-w-2xl leading-7 text-white/75">Explore available cars with clear pricing, useful specifications, and photos that help you shortlist with confidence.</p>
                    </div>
                    <p class="mt-auto border-t border-white/15 pt-5 text-sm font-medium text-white/85">Clear pricing &middot; Useful specifications &middot; Detailed vehicle photos</p>
                </article>

                <article class="rounded-card border border-border-default bg-pearl p-7 sm:p-8" data-reveal>
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-button bg-pure-white text-carbon">
                        <x-heroicon-o-magnifying-glass class="h-5 w-5" aria-hidden="true" />
                    </span>
                    <h3 class="mt-6 font-display text-xl font-medium">Viewing and selection support</h3>
                    <p class="mt-3 leading-7 text-text-secondary">Tell us what matters to you. We will help narrow the options and arrange a viewing at the appropriate Lagos location.</p>
                </article>

                <article class="rounded-card border border-border-default bg-pearl p-7 sm:p-8" data-reveal>
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-button bg-pure-white text-carbon">
                        <x-heroicon-o-document-check class="h-5 w-5" aria-hidden="true" />
                    </span>
                    <h3 class="mt-6 font-display text-xl font-medium">Reservation assistance</h3>
                    <p class="mt-3 leading-7 text-text-secondary">When you are ready, our team explains the reservation terms and next steps clearly before you commit.</p>
                </article>

                <article class="rounded-card border border-border-default bg-pearl p-7 sm:p-8 lg:col-start-1" data-reveal>
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-button bg-pure-white text-carbon">
                        <x-heroicon-o-truck class="h-5 w-5" aria-hidden="true" />
                    </span>
                    <h3 class="mt-6 font-display text-xl font-medium">Nationwide delivery</h3>
                    <p class="mt-3 leading-7 text-text-secondary">Buying outside Lagos? Speak with the team about delivery availability and arrangements for your destination.</p>
                </article>
            </div>
        </div>
    </section>

    <section class="bg-pearl py-section" aria-labelledby="process-heading">
        <div class="mx-auto max-w-site px-gutter">
            <div class="grid gap-12 lg:grid-cols-[0.85fr_1.15fr] lg:gap-24">
                <div class="max-w-md" data-reveal="left">
                    <p class="text-xs font-semibold uppercase tracking-label text-text-secondary">A simpler process</p>
                    <h2 id="process-heading" class="mt-5 font-display text-h1 font-medium tracking-display">Three clear steps to your next car</h2>
                    <p class="mt-5 leading-7 text-text-secondary">A straightforward path from finding the right vehicle to collection or delivery.</p>
                </div>

                <ol class="grid gap-0" data-reveal-group>
                    <li class="grid grid-cols-[3.25rem_1fr] gap-5 border-b border-border-default pb-7" data-reveal>
                        <span class="inline-flex h-11 w-11 items-center justify-center rounded-full bg-mercy-red text-sm font-semibold text-pure-white">01</span>
                        <div><h3 class="font-display text-xl font-medium">Choose a vehicle</h3><p class="mt-2 leading-7 text-text-secondary">Browse the inventory or tell us your preferred make, budget, and must-have features.</p></div>
                    </li>
                    <li class="grid grid-cols-[3.25rem_1fr] gap-5 border-b border-border-default py-7" data-reveal>
                        <span class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-mercy-red text-sm font-semibold text-mercy-red">02</span>
                        <div><h3 class="font-display text-xl font-medium">Talk and inspect</h3><p class="mt-2 leading-7 text-text-secondary">Ask questions, arrange a viewing, and inspect the vehicle before making your decision.</p></div>
                    </li>
                    <li class="grid grid-cols-[3.25rem_1fr] gap-5 pt-7" data-reveal>
                        <span class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-mercy-red text-sm font-semibold text-mercy-red">03</span>
                        <div><h3 class="font-display text-xl font-medium">Complete and collect</h3><p class="mt-2 leading-7 text-text-secondary">Confirm the terms with our team, complete payment, then collect or arrange delivery.</p></div>
                    </li>
                </ol>
            </div>

            <div class="mt-16 border-t border-border-default pt-8" data-reveal>
                <ul class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach (['Two Lagos locations', 'Direct team assistance', 'Inspection before purchase', 'Nationwide delivery arrangements'] as $trustPoint)
                        <li class="flex items-start gap-3 text-sm font-semibold text-carbon">
                            <x-heroicon-o-check-circle class="mt-0.5 h-5 w-5 shrink-0 text-mercy-red" aria-hidden="true" />
                            <span>{{ $trustPoint }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </section>

    <section class="border-b border-white/10 bg-carbon py-14 text-pure-white sm:py-16">
        <div class="mx-auto flex max-w-site flex-col items-start justify-between gap-8 px-gutter md:flex-row md:items-center" data-reveal>
            <div>
                <p class="text-xs font-semibold uppercase tracking-label text-gold">Ready when you are</p>
                <h2 class="mt-3 font-display text-section-title font-medium tracking-display">Start with the cars available today.</h2>
                <p class="mt-3 max-w-2xl leading-7 text-white/65">Browse the current inventory or contact our team when you need personal guidance.</p>
            </div>
            <div class="flex w-full shrink-0 flex-col gap-4 sm:w-auto sm:flex-row sm:items-center">
                <a href="{{ route('cars.index') }}" class="btn-primary rounded-full px-8">Browse inventory</a>
                <a href="{{ route('contact') }}" class="inline-flex min-h-11 items-center justify-center text-sm font-semibold text-pure-white underline decoration-white/35 decoration-2 underline-offset-4 transition-colors hover:decoration-pure-white">Contact Us</a>
            </div>
        </div>
    </section>
</x-layouts.public-layout>
