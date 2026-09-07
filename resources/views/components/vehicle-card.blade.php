@props(['car'])

@php
    $vehicleName = trim(implode(' ', array_filter([$car->make, $car->model, $car->trim])));
    $accessibleVehicleName = trim($car->year.' '.$vehicleName);
    $image = $car->coverImage;
    $hasDiscount = $car->previous_price_amount && $car->previous_price_amount > $car->price_amount;
    $categoryClass = match ($car->listing_category->value) {
        'brand_new' => 'bg-mercy-red text-pure-white',
        'foreign_used' => 'bg-available text-pure-white',
        'pre_order' => 'bg-gold text-carbon',
        default => 'bg-mercy-red text-pure-white',
    };
@endphp

<article class="group relative flex h-full cursor-pointer flex-col overflow-hidden rounded-card bg-pure-white shadow-card transition-[transform,box-shadow] duration-standard ease-brand hover:-translate-y-1 hover:shadow-card-hover focus-within:shadow-card-hover">
    <div class="relative">
        <a href="{{ route('cars.show', $car) }}" class="block aspect-vehicle-card overflow-hidden bg-pearl" aria-label="View {{ $accessibleVehicleName }}">
            @if ($image)
                <picture>
                    @if ($image->hasVariant('card', 'avif'))<source type="image/avif" srcset="{{ $image->srcset('avif') }}" sizes="(min-width: 768px) 33vw, 100vw">@endif
                    <img src="{{ $image->variantUrl('card') }}" srcset="{{ $image->srcset() }}" sizes="(min-width: 768px) 33vw, 100vw" alt="{{ $image->alt_text ?: $vehicleName }}" loading="lazy" class="h-full w-full object-cover transition-transform duration-slow group-hover:scale-[1.025]">
                </picture>
            @else
                <span class="flex h-full items-center justify-center text-sm text-text-secondary">Vehicle imagery is being prepared</span>
            @endif
            <span class="absolute left-4 top-4 rounded-lg {{ $categoryClass }} px-3.5 py-2 text-xs font-semibold capitalize shadow-sm">{{ $car->listing_category->label() }}</span>
        </a>
        <button type="button" class="absolute right-4 top-4 z-20 inline-flex h-12 w-12 items-center justify-center rounded-full bg-pure-white text-carbon shadow-card hover:text-mercy-red" data-save-vehicle="{{ $car->slug }}" aria-label="Save {{ $accessibleVehicleName }}" aria-pressed="false">
            <x-heroicon-o-heart class="h-5 w-5" aria-hidden="true" data-save-icon-outline />
            <x-heroicon-s-heart class="hidden h-5 w-5 text-mercy-red" aria-hidden="true" data-save-icon-filled />
        </button>
    </div>
    <div class="flex flex-1 flex-col p-6">
        <p class="text-sm font-semibold uppercase tracking-label text-mercy-red">{{ $car->body_type ?: 'Vehicle' }}</p>
        <h3 class="mt-2 font-display text-xl font-semibold tracking-display text-carbon"><a href="{{ route('cars.show', $car) }}" class="after:absolute after:inset-0">{{ $vehicleName }}</a></h3>
        <div class="mt-4 flex flex-wrap gap-x-5 gap-y-2 text-sm text-text-secondary">
            <span>{{ $car->year }}</span>
            @if ($car->transmission)<span>{{ $car->transmission->label() }}</span>@endif
            @if ($car->fuel_type)<span>{{ $car->fuel_type->label() }}</span>@endif
        </div>
        <div class="mt-6 flex items-center justify-between gap-3 border-t border-border-default pt-5">
            <div>
                @if ($hasDiscount)<p class="font-display text-base text-text-secondary line-through">₦{{ number_format($car->previous_price_amount) }}</p>@endif
                <p class="font-display text-[1.375rem] font-semibold tracking-display text-mercy-red">₦{{ number_format($car->price_amount) }}</p>
            </div>
            <a href="{{ route('cars.show', $car) }}" class="relative z-20 inline-flex min-h-11 min-w-28 items-center justify-between gap-4 rounded-button border border-border-default px-4 text-sm font-semibold hover:border-carbon">
                <span>View</span>
                <x-heroicon-o-arrow-right class="h-4 w-4 shrink-0" aria-hidden="true" />
            </a>
        </div>
    </div>
</article>
