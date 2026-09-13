@php
    $vehicleName = trim(implode(' ', array_filter([$car->year, $car->make, $car->model, $car->trim])));
    $messageUrl = $business['whatsapp_url'].'?text='.rawurlencode("Hello Auto Mercy, I am interested in the {$vehicleName}.");
    $galleryImages = $car->images->sortBy('sort_order')->values();
    $activeImage = $car->coverImage ?? $galleryImages->first();
    $initialGalleryIndex = $activeImage
        ? $galleryImages->search(fn ($image): bool => $image->is($activeImage))
        : 0;
    $initialGalleryIndex = $initialGalleryIndex === false ? 0 : $initialGalleryIndex;
    $hasDiscount = $car->previous_price_amount && $car->previous_price_amount > $car->price_amount;
    $specifications = [
        ['Engine', $car->engine ?: 'Not listed'], ['Transmission', $car->transmission?->label() ?? 'Not listed'],
        ['Fuel type', $car->fuel_type?->label() ?? 'Not listed'], ['Exterior colour', $car->exterior_colour ?: 'Not listed'],
        ['Interior colour', $car->interior_colour ?: 'Not listed'], ['Drive type', $car->drivetrain?->label() ?? 'Not listed'],
        ['Mileage', $car->mileage === null ? 'Not listed' : number_format($car->mileage).' '.($car->mileage_unit?->value ?? '')], ['Body type', $car->body_type ?: 'Not listed'],
    ];
@endphp

<x-layouts.public-layout :title="$vehicleName.' | Auto Mercy'" :description="Str::limit(strip_tags($car->description), 155)" :canonical="route('cars.show', $car)" :image="$activeImage?->url">
    <section class="bg-pearl py-section-compact">
        <div class="mx-auto max-w-wide px-gutter">
            @if ($isPreview ?? false)<div class="mb-6 rounded-card border border-mercy-red/30 bg-mercy-red/5 px-5 py-4 text-sm"><strong>Staff preview:</strong> this vehicle is {{ $car->status->label() }}.</div>@endif
            <section class="grid items-start gap-10 lg:grid-cols-2 lg:gap-16">
                <div
                    class="min-w-0"
                    data-vehicle-gallery
                    data-gallery-initial-index="{{ $initialGalleryIndex }}"
                    data-gallery-autoplay-delay="4500"
                >
                    <div class="relative aspect-[5/4] overflow-hidden rounded-card bg-pure-white">
                        @if ($galleryImages->isNotEmpty())
                            <div class="swiper vehicle-gallery-swiper h-full" data-gallery-swiper>
                                <div class="swiper-wrapper">
                                    @foreach ($galleryImages as $index => $image)
                                        <div
                                            class="swiper-slide"
                                            data-gallery-slide
                                            data-gallery-image="{{ $image->variantUrl('large') }}"
                                            data-gallery-srcset="{{ $image->srcset() }}"
                                            data-gallery-alt="{{ $image->alt_text ?: $vehicleName }}"
                                        >
                                            <button
                                                type="button"
                                                class="group relative block h-full w-full cursor-zoom-in"
                                                data-gallery-lightbox-trigger
                                                aria-label="Open photo {{ $index + 1 }} of {{ $galleryImages->count() }} in full screen"
                                            >
                                                <img
                                                    src="{{ $image->variantUrl('large') }}"
                                                    srcset="{{ $image->srcset() }}"
                                                    alt="{{ $image->alt_text ?: $vehicleName }}"
                                                    class="h-full w-full object-cover"
                                                    @if ($index !== $initialGalleryIndex) loading="lazy" @else fetchpriority="high" @endif
                                                >
                                                <span class="absolute bottom-4 right-4 inline-flex min-h-10 items-center gap-2 rounded-button bg-carbon/80 px-3 text-xs font-semibold text-pure-white opacity-100 backdrop-blur-sm transition-opacity sm:opacity-0 sm:group-hover:opacity-100 sm:group-focus-visible:opacity-100">
                                                    <x-heroicon-o-arrows-pointing-out class="h-4 w-4" aria-hidden="true" />
                                                    <span class="hidden sm:inline">Full screen</span>
                                                </span>
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <div class="flex h-full items-center justify-center text-text-secondary">Vehicle imagery is being prepared</div>
                        @endif

                        <button type="button" class="absolute right-4 top-4 z-10 inline-flex min-h-11 items-center gap-2 rounded-full bg-pure-white/95 px-5 text-sm font-medium text-carbon shadow-card backdrop-blur-sm transition-[transform,box-shadow,color] hover:-translate-y-px hover:text-mercy-red hover:shadow-card-hover" data-share-vehicle data-share-title="{{ $vehicleName }} | Auto Mercy" aria-label="Share {{ $vehicleName }}">
                            <x-heroicon-o-share class="h-5 w-5 shrink-0" aria-hidden="true" />
                            <span data-share-vehicle-label>Share</span>
                        </button>
                        <span class="sr-only" role="status" aria-live="polite" data-share-vehicle-status></span>

                        @if ($galleryImages->count() > 1)
                            <button type="button" class="vehicle-gallery-navigation left-3" data-gallery-previous aria-label="Previous vehicle photo">
                                <x-heroicon-o-chevron-left class="h-5 w-5" aria-hidden="true" />
                            </button>
                            <button type="button" class="vehicle-gallery-navigation right-3" data-gallery-next aria-label="Next vehicle photo">
                                <x-heroicon-o-chevron-right class="h-5 w-5" aria-hidden="true" />
                            </button>
                        @endif
                    </div>

                    @if ($galleryImages->count() > 1)
                        <div class="mt-3 flex gap-2 overflow-x-auto px-1 pb-2 pt-1 [scrollbar-width:thin]" aria-label="Choose a vehicle photo">
                            @foreach ($galleryImages as $index => $image)
                                <button
                                    type="button"
                                    class="aspect-[4/3] w-24 shrink-0 overflow-hidden rounded-lg border-2 border-transparent bg-pearl sm:w-28"
                                    data-gallery-thumbnail
                                    data-gallery-index="{{ $index }}"
                                    aria-label="Show vehicle photo {{ $index + 1 }}"
                                    aria-pressed="{{ $index === $initialGalleryIndex ? 'true' : 'false' }}"
                                >
                                    <img src="{{ $image->variantUrl('thumbnail') }}" alt="" class="h-full w-full object-cover" loading="lazy">
                                </button>
                            @endforeach
                        </div>
                    @endif

                    @if ($galleryImages->isNotEmpty())
                        <div
                            class="vehicle-gallery-lightbox bg-carbon/95 text-pure-white"
                            data-gallery-lightbox
                            hidden
                            role="dialog"
                            aria-modal="true"
                            aria-label="{{ $vehicleName }} photo gallery"
                            aria-hidden="true"
                            tabindex="-1"
                        >
                            <div class="relative flex h-full w-full items-center justify-center px-4 py-16 sm:px-20" data-gallery-lightbox-stage>
                                <button type="button" class="vehicle-lightbox-control right-4 top-4" data-gallery-lightbox-close aria-label="Close full-screen gallery">
                                    <x-heroicon-o-x-mark class="h-6 w-6" aria-hidden="true" />
                                </button>

                                @if ($galleryImages->count() > 1)
                                    <button type="button" class="vehicle-lightbox-control left-3 top-1/2 -translate-y-1/2 sm:left-6" data-gallery-lightbox-previous aria-label="Previous photo">
                                        <x-heroicon-o-chevron-left class="h-6 w-6" aria-hidden="true" />
                                    </button>
                                @endif

                                <img
                                    src="{{ $activeImage?->variantUrl('large') }}"
                                    srcset="{{ $activeImage?->srcset() }}"
                                    alt="{{ $activeImage?->alt_text ?: $vehicleName }}"
                                    class="vehicle-gallery-lightbox-image max-h-full max-w-full select-none object-contain"
                                    data-gallery-lightbox-image
                                >

                                @if ($galleryImages->count() > 1)
                                    <button type="button" class="vehicle-lightbox-control right-3 top-1/2 -translate-y-1/2 sm:right-6" data-gallery-lightbox-next aria-label="Next photo">
                                        <x-heroicon-o-chevron-right class="h-6 w-6" aria-hidden="true" />
                                    </button>
                                    <p class="absolute bottom-5 left-1/2 -translate-x-1/2 text-xs font-semibold tracking-label text-white/80" data-gallery-lightbox-count></p>
                                @endif
                            </div>
                        </div>
                    @endif

                    <div class="mt-5 rounded-card border border-border-default border-l-2 border-l-gold bg-pure-white px-5 py-4 text-sm text-text-secondary"><strong class="font-medium text-carbon">Viewing note:</strong> Call before visiting so our team can confirm availability and arrange an inspection.</div>
                </div>
                <div class="min-w-0">
                    <div class="flex flex-wrap gap-2"><span class="rounded-full bg-mercy-red px-4 py-2 text-xs font-medium text-pure-white">{{ $car->listing_category->label() }}</span></div>
                    <h1 class="mt-6 font-display text-[clamp(2.25rem,4vw,3rem)] font-medium leading-[1.05] tracking-display text-carbon">{{ $vehicleName }}</h1>
                    <div class="mt-7 flex flex-wrap items-baseline gap-3"><p class="font-display text-[clamp(2rem,4vw,2.75rem)] font-medium leading-none tracking-display text-mercy-red">&#8358;{{ number_format($car->price_amount) }}</p>@if ($hasDiscount)<p class="font-display text-base text-text-secondary line-through">&#8358;{{ number_format($car->previous_price_amount) }}</p>@endif</div>
                    <dl class="mt-9 grid grid-cols-2 border-t border-border-default">@foreach ($specifications as [$label, $value])<div class="border-b border-border-default py-4 pr-4"><dt class="text-xs font-semibold uppercase tracking-label text-text-secondary">{{ $label }}</dt><dd class="mt-2 text-base font-medium text-carbon">{{ $value }}</dd></div>@endforeach</dl>
                    @if (session('enquiry_success'))<p class="mt-6 bg-status-available/10 px-4 py-3 text-sm font-semibold text-status-available">{{ session('enquiry_success') }}</p>@endif
                    <div class="mt-8 flex flex-col gap-3 sm:flex-row">@unless ($isPreview ?? false)<button type="button" class="btn-primary flex-1 rounded-full px-7 font-medium" data-request-modal-trigger="vehicle-enquiry-modal">Request a callback</button>@endunless<a href="{{ $messageUrl }}" class="btn-secondary flex-1 rounded-full px-7 font-medium" target="_blank" rel="noopener">Ask on WhatsApp</a></div>
                </div>
            </section>
        </div>
    </section>

    <section class="bg-pure-white py-section-compact" aria-label="Vehicle information">
        <div class="mx-auto grid max-w-site gap-12 px-gutter lg:grid-cols-[1.1fr_0.9fr] lg:gap-24">
            <div>
                <p class="text-sm font-semibold uppercase tracking-label text-text-secondary">Vehicle overview</p>
                <h2 class="mt-5 font-display text-h1 font-medium">About this {{ $car->make }} {{ $car->model }}</h2>
                <div class="mt-6 whitespace-pre-line text-base leading-8 text-text-secondary sm:text-lg">{{ trim(strip_tags($car->description)) }}</div>
            </div>
            <div>
                <p class="text-sm font-semibold uppercase tracking-label text-text-secondary">Features and equipment</p>
                <h2 class="mt-5 font-display text-h1 font-medium">What's included</h2>
                @if ($car->features->isNotEmpty())
                    <ul class="mt-7 grid grid-cols-1 gap-x-10 gap-y-4 sm:grid-cols-2">
                        @foreach ($car->features as $feature)
                            <li class="flex items-start gap-3 font-medium text-carbon">
                                <x-heroicon-o-check class="mt-0.5 h-5 w-5 shrink-0 text-mercy-red" aria-hidden="true" />
                                <span>{{ $feature->name }}</span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="mt-5 text-sm text-text-secondary">Ask our team for the complete equipment list.</p>
                @endif
            </div>
        </div>
    </section>

    @if ($relatedCars->isNotEmpty())<section class="bg-pearl py-section-compact"><div class="mx-auto max-w-site px-gutter"><h2 class="font-display text-h2 font-medium">You might also like</h2><div class="mt-8 grid gap-grid md:grid-cols-2 lg:grid-cols-3">@foreach ($relatedCars as $relatedCar)<x-vehicle-card :car="$relatedCar" />@endforeach</div></div></section>@endif
    @unless ($isPreview ?? false)
        <dialog id="vehicle-enquiry-modal" class="fixed inset-0 m-auto w-[calc(100%-2rem)] max-w-xl rounded-card bg-white p-0 shadow-overlay backdrop:bg-carbon/75" data-request-modal data-open-on-load="{{ $errors->any() ? 'true' : 'false' }}">
            <div class="flex justify-between border-b border-border-default p-5"><div><p class="text-xs font-semibold uppercase text-mercy-red">Vehicle enquiry</p><h2 class="mt-2 font-display text-2xl font-medium">Request a callback</h2></div><button type="button" class="text-2xl font-medium" data-request-modal-close aria-label="Close">&times;</button></div>
            <form method="POST" action="{{ route('cars.enquiries.store', $car) }}" class="grid gap-4 p-6 sm:grid-cols-2">@csrf<div class="hidden"><input name="company" tabindex="-1"></div><label class="text-sm font-medium">Name<input name="customer_name" value="{{ old('customer_name') }}" required class="field-control mt-2">@error('customer_name')<span class="text-xs text-mercy-red">{{ $message }}</span>@enderror</label><label class="text-sm font-medium">Phone<input name="phone" value="{{ old('phone') }}" required class="field-control mt-2">@error('phone')<span class="text-xs text-mercy-red">{{ $message }}</span>@enderror</label><label class="text-sm font-medium sm:col-span-2">Email (optional)<input type="email" name="email" value="{{ old('email') }}" class="field-control mt-2"></label><label class="text-sm font-medium sm:col-span-2">Message<textarea name="message" required rows="4" class="field-control mt-2 h-auto min-h-32 py-3">{{ old('message', 'I am interested in this '.$vehicleName.'.') }}</textarea></label><div class="flex justify-end gap-3 sm:col-span-2"><button type="button" class="btn-secondary rounded-full px-6 font-medium" data-request-modal-close>Cancel</button><button type="submit" class="btn-primary rounded-full px-6 font-medium">Send enquiry</button></div></form>
        </dialog>
    @endunless
</x-layouts.public-layout>
