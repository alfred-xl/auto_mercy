<x-layouts.public-layout
    :title="$landing['title']"
    :description="$landing['description']"
    :canonical="$canonical"
    :robots="$robots"
    :structured-data="$structuredData"
    page-type="inventory"
>
    <header class="bg-carbon py-section-compact text-pure-white">
        <div class="mx-auto max-w-site px-gutter">
            <nav aria-label="Breadcrumb">
                <ol class="flex flex-wrap items-center gap-2 text-sm text-metallic">
                    @foreach ($breadcrumbs as $index => $breadcrumb)
                        <li class="flex items-center gap-2">
                            @if (! $loop->last)
                                <a href="{{ $breadcrumb['url'] }}" class="transition-colors hover:text-pure-white">{{ $breadcrumb['name'] }}</a>
                                <span aria-hidden="true">/</span>
                            @else
                                <span aria-current="page">{{ $breadcrumb['name'] }}</span>
                            @endif
                        </li>
                    @endforeach
                </ol>
            </nav>
            <h1 class="mt-8 max-w-4xl font-display text-[clamp(2.5rem,5vw,4rem)] font-medium leading-none tracking-display">{{ $landing['heading'] }}</h1>
            <p class="mt-6 max-w-3xl text-base leading-8 text-white/75 sm:text-lg">{{ $landing['intro'] }}</p>
            <p class="mt-4 max-w-3xl text-sm text-white/65">Auto Mercy has one shared inventory across two Lagos locations. Contact the team to confirm where a vehicle can be viewed before visiting.</p>
        </div>
    </header>

    <section class="bg-pearl py-section" aria-labelledby="landing-results-heading">
        <div class="mx-auto max-w-site px-gutter">
            <div class="flex flex-col gap-4 border-b border-border-default pb-6 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-label text-mercy-red">Current shared inventory</p>
                    <h2 id="landing-results-heading" class="mt-3 font-display text-h2 font-medium">{{ $cars->total() }} matching {{ Str::plural('vehicle', $cars->total()) }}</h2>
                </div>
                <a href="{{ route('cars.index') }}" class="text-link">Browse all cars <x-heroicon-o-arrow-right class="h-4 w-4" aria-hidden="true" /></a>
            </div>

            @if ($cars->isNotEmpty())
                <div class="mt-8 grid gap-grid md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($cars as $car)
                        <x-vehicle-card :car="$car" sizes="(min-width: 1280px) 380px, (min-width: 1024px) calc((100vw - 4rem) / 3), (min-width: 768px) calc((100vw - 4rem) / 2), calc(100vw - 2rem)" />
                    @endforeach
                </div>

                @if ($cars->hasPages())
                    <div class="mt-10">{{ $cars->onEachSide(1)->links() }}</div>
                @endif
            @else
                <div class="mt-8 rounded-card border border-border-default bg-pure-white px-6 py-12 text-center">
                    <h2 class="font-display text-2xl font-medium">No matching cars are currently published</h2>
                    <p class="mx-auto mt-3 max-w-2xl leading-7 text-text-secondary">This page remains available to explain the selection, but it is temporarily excluded from search indexing and the sitemap. Contact Auto Mercy for current options.</p>
                    <div class="mt-6 flex flex-wrap justify-center gap-3">
                        @foreach ((array) config('automercy.business.phones') as $phoneNumber)
                            <a href="{{ $phoneNumber['telephone_url'] }}" class="btn-secondary" data-analytics-cta="empty_state">Call {{ $phoneNumber['display'] }}</a>
                        @endforeach
                        <a href="{{ config('automercy.business.whatsapp_url') }}" class="btn-primary" target="_blank" rel="noopener" data-analytics-cta="empty_state">Ask on WhatsApp</a>
                    </div>
                </div>
            @endif

            @if ($relatedLandings !== [])
                <nav class="mt-12 border-t border-border-default pt-7" aria-label="Related car searches">
                    <p class="text-sm font-semibold text-carbon">Explore related published searches</p>
                    <div class="mt-4 flex flex-wrap gap-3">
                        @foreach ($relatedLandings as $relatedLanding)
                            <a href="{{ $relatedLanding['url'] }}" class="rounded-full border border-border-default bg-pure-white px-4 py-2 text-sm font-medium transition-colors hover:border-mercy-red hover:text-mercy-red">{{ $relatedLanding['name'] }}</a>
                        @endforeach
                    </div>
                </nav>
            @endif
        </div>
    </section>
</x-layouts.public-layout>
