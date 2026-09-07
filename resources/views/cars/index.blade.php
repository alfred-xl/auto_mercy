<x-layouts.public-layout
    title="Vehicle Inventory in Lagos | Auto Mercy"
    description="Browse brand-new, foreign-used, and pre-order cars currently available from Auto Mercy. Filter by category, make, model, specifications, and price."
    :canonical="$canonical"
    :robots="$robots"
>
    <header class="relative isolate flex min-h-[18rem] items-center justify-center overflow-hidden bg-carbon text-center text-pure-white sm:min-h-[20rem]">
        <img src="{{ asset('images/auto-mercy-hero.webp') }}" width="1792" height="1024" alt="" fetchpriority="high" class="absolute inset-0 -z-20 h-full w-full object-cover object-center">
        <div class="absolute inset-0 -z-10 bg-carbon/60" aria-hidden="true"></div>
        <div class="mx-auto w-full max-w-site px-gutter py-16">
            <h1 class="font-display text-h1 font-semibold tracking-display">Inventory</h1>
            <p class="mx-auto mt-4 max-w-2xl text-base leading-7 text-white/80 sm:text-lg">Browse quality brand-new, foreign-used, and pre-order vehicles available from Auto Mercy.</p>
        </div>
    </header>

    <div id="inventory-filter-drawer" class="fixed inset-x-0 bottom-[calc(4rem+env(safe-area-inset-bottom))] top-0 z-[70] hidden lg:hidden" data-inventory-filter-drawer aria-hidden="true">
        <button type="button" class="absolute inset-0 bg-carbon/65 backdrop-blur-[2px]" data-inventory-filter-backdrop tabindex="-1" aria-label="Close filters"></button>
        <section class="absolute inset-y-0 right-0 flex w-full max-w-md flex-col bg-pure-white shadow-overlay" role="dialog" aria-modal="true" aria-labelledby="mobile-filter-title" data-inventory-filter-panel>
            <div class="flex items-center justify-between gap-4 border-b border-border-default px-5 py-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-label text-mercy-red">Refine inventory</p>
                    <h2 id="mobile-filter-title" class="mt-1 text-xl font-semibold text-carbon">Filters</h2>
                </div>
                <button type="button" class="inline-flex h-11 w-11 items-center justify-center rounded-button border border-border-default text-carbon transition-colors hover:border-carbon" data-inventory-filter-close aria-label="Close filters">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m6 6 12 12M18 6 6 18"/></svg>
                </button>
            </div>
            <div class="min-h-0 flex-1 overflow-y-auto overscroll-contain px-5 py-6 pb-[calc(1.5rem+env(safe-area-inset-bottom))]">
                <x-inventory-filters
                    :filters="$filters"
                    :categories="$categories"
                    :makes="$makes"
                    :models="$models"
                    :body-types="$bodyTypes"
                    :transmissions="$transmissions"
                    :fuels="$fuels"
                    prefix="mobile-inventory"
                    :mobile="true"
                />
            </div>
        </section>
    </div>

    <section class="bg-surface-secondary py-section" aria-labelledby="inventory-results-heading">
        <div class="mx-auto grid max-w-site gap-8 px-gutter lg:grid-cols-[minmax(17.5rem,20rem)_minmax(0,1fr)] lg:items-start">
            <aside class="hidden rounded-card border border-border-default bg-pure-white p-5 lg:sticky lg:top-6 lg:block lg:max-h-[calc(100vh-3rem)] lg:overflow-y-auto" aria-label="Filter available cars">
                <div class="mb-6 border-b border-border-default pb-5">
                    <p class="text-xs font-semibold uppercase tracking-label text-mercy-red">Find your car</p>
                    <h2 class="mt-2 text-xl font-semibold text-carbon">Filter inventory</h2>
                </div>
                <x-inventory-filters
                    :filters="$filters"
                    :categories="$categories"
                    :makes="$makes"
                    :models="$models"
                    :body-types="$bodyTypes"
                    :transmissions="$transmissions"
                    :fuels="$fuels"
                    prefix="desktop-inventory"
                />
            </aside>

            <div class="min-w-0" id="inventory-results" data-inventory-results aria-busy="false">
                <div class="rounded-card border border-border-default bg-pure-white p-4 md:p-5">
                    <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
                        <div>
                            <p id="inventory-results-heading" class="text-lg font-semibold text-carbon" tabindex="-1">{{ $cars->total() }} {{ Str::plural('car', $cars->total()) }} found</p>
                            <p class="mt-1 text-sm text-text-secondary">Published inventory from our Lagos locations.</p>
                        </div>
                        <div class="flex flex-wrap items-end gap-3">
                            <button type="button" class="btn-secondary relative px-4 lg:hidden" data-inventory-filter-trigger aria-expanded="false" aria-controls="inventory-filter-drawer">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 6h16M7 12h10M10 18h4"/></svg>
                                Filters
                                @if ($activeFilterCount > 0)<span class="inline-flex h-6 min-w-6 items-center justify-center rounded-full bg-mercy-red px-1.5 text-xs text-pure-white">{{ $activeFilterCount }}</span>@endif
                            </button>
                            <form action="{{ route('cars.index') }}" method="GET" class="flex items-end gap-2" data-inventory-sort-form>
                                @foreach ($normalizedFilters as $key => $value)
                                    @continue(in_array($key, ['sort', 'page'], true))
                                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                @endforeach
                                <div>
                                    <label for="inventory-sort" class="field-label">Sort by</label>
                                    <select id="inventory-sort" name="sort" class="field-control min-w-48" data-inventory-sort>
                                        @foreach ($sortOptions as $value => $label)
                                            <option value="{{ $value }}" @selected(($filters['sort'] ?? 'latest') === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit" class="sr-only focus:not-sr-only focus:btn-secondary">Apply sort</button>
                            </form>
                            <button type="button" class="inline-flex h-12 items-center justify-center gap-2 rounded-button border border-border-default bg-pure-white px-4 text-sm font-semibold text-carbon transition-colors hover:border-carbon" data-share-inventory>
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="18" cy="5" r="2.5"/><circle cx="6" cy="12" r="2.5"/><circle cx="18" cy="19" r="2.5"/><path d="m8.2 10.8 7.6-4.5M8.2 13.2l7.6 4.5"/></svg>
                                Share
                            </button>
                        </div>
                    </div>
                    <p class="mt-3 hidden text-sm font-medium text-mercy-red" data-inventory-loading role="status">Updating results…</p>
                    <p class="sr-only" aria-live="polite" data-inventory-share-status></p>
                </div>

                @if ($activeFilters !== [])
                    <div class="mt-5 flex flex-wrap items-center gap-2" aria-label="Active filters">
                        @foreach ($activeFilters as $filter)
                            <a href="{{ $filter['url'] }}" class="inline-flex min-h-10 items-center gap-2 rounded-full border border-border-default bg-pure-white px-3 py-2 text-sm font-medium text-carbon transition-colors hover:border-carbon" aria-label="Remove {{ $filter['label'] }} filter">
                                <span>{{ $filter['label'] }}</span>
                                <svg class="h-3.5 w-3.5" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path d="m3 3 10 10M13 3 3 13"/></svg>
                            </a>
                        @endforeach
                        <a href="{{ route('cars.index') }}" class="min-h-10 px-3 py-2 text-sm font-semibold text-mercy-red hover:underline">Clear All</a>
                    </div>
                @endif

                @if ($hasFilterErrors)
                    <div class="mt-7 rounded-card border border-mercy-red/30 bg-pure-white px-6 py-12 text-center">
                        <h2 class="text-2xl font-semibold">Some filters need attention</h2>
                        <p class="mx-auto mt-3 max-w-xl text-text-secondary">Review the highlighted values and apply the filters again. No inventory has been shown for the invalid request.</p>
                        <button type="button" class="btn-primary mt-6 lg:hidden" data-inventory-filter-trigger aria-expanded="false" aria-controls="inventory-filter-drawer">Review Filters</button>
                    </div>
                @elseif ($cars->isNotEmpty())
                    <div class="mt-7 grid gap-grid md:grid-cols-2">
                        @foreach ($cars as $car)
                            <x-vehicle-card :car="$car" />
                        @endforeach
                    </div>
                    @if ($cars->hasPages())
                        <div class="mt-10">{{ $cars->onEachSide(1)->links() }}</div>
                    @endif
                @elseif (! $hasPublicInventory)
                    <div class="mt-7 rounded-card border border-border-default bg-pure-white px-6 py-14 text-center">
                        <img src="{{ asset('images/auto-mercy-logo.webp') }}" width="80" height="80" alt="" loading="lazy" class="mx-auto h-20 w-20 object-contain opacity-70">
                        <h2 class="mt-5 text-2xl font-semibold">Available cars are being updated</h2>
                        <p class="mx-auto mt-3 max-w-xl text-text-secondary">There are no active vehicles online right now. Call or message Auto Mercy to confirm the current inventory.</p>
                        <div class="mt-6 flex flex-wrap justify-center gap-3"><a href="{{ config('automercy.business.telephone_url') }}" class="btn-secondary">Call {{ config('automercy.business.phone_display') }}</a><a href="{{ config('automercy.business.whatsapp_url') }}" class="btn-primary" target="_blank" rel="noopener">Ask on WhatsApp</a></div>
                    </div>
                @else
                    <div class="mt-7 rounded-card border border-border-default bg-pure-white px-6 py-14 text-center">
                        <h2 class="text-2xl font-semibold">No cars match these filters</h2>
                        <p class="mx-auto mt-3 max-w-xl text-text-secondary">Remove one criterion above or clear all filters to browse the complete Available inventory.</p>
                        <div class="mt-6 flex flex-wrap justify-center gap-3"><a href="{{ route('cars.index') }}" class="btn-secondary">Clear All</a><a href="{{ config('automercy.business.telephone_url') }}" class="btn-secondary">Call Auto Mercy</a><a href="{{ config('automercy.business.whatsapp_url') }}" class="btn-primary" target="_blank" rel="noopener">Ask on WhatsApp</a></div>
                    </div>
                @endif
            </div>
        </div>
    </section>
</x-layouts.public-layout>
