@props(['car'])

@php
    $vehicleName = trim(implode(' ', array_filter([$car->year, $car->make?->name, $car->carModel?->name])));
    $accessibleName = trim(implode(' ', array_filter([$vehicleName, $car->trim])));
    $statusLabel = $car->status?->label() ?? 'Status unavailable';
    $statusClass = match ($car->status?->value) {
        'available' => 'bg-status-available',
        'reserved' => 'bg-status-reserved',
        'sold' => 'bg-status-sold',
        default => 'bg-status-newly-added',
    };
    $mileage = $car->mileage !== null
        ? trim(number_format((float) $car->mileage).' '.($car->mileage_unit?->value ?? ''))
        : null;
@endphp

<article data-vehicle-card class="group flex h-full flex-col overflow-hidden rounded-card border border-border-default bg-pure-white shadow-card transition-[transform,box-shadow,border-color] duration-standard ease-brand hover:-translate-y-0.5 hover:border-border-strong hover:shadow-card-hover">
    <div class="relative">
        <a href="{{ route('cars.show', $car) }}" class="relative block aspect-vehicle-card overflow-hidden bg-pearl" aria-label="View details for {{ $accessibleName }}">
            @if ($car->primaryImage)
                <img src="{{ $car->primaryImage->url }}" width="{{ $car->primaryImage->width ?? 1600 }}" height="{{ $car->primaryImage->height ?? 1200 }}" alt="{{ $car->primaryImage->alt_text ?: $accessibleName }}" loading="lazy" class="h-full w-full object-cover transition-transform duration-slow ease-brand group-hover:scale-[1.025]">
            @else
                <span class="flex h-full flex-col items-center justify-center gap-3 px-5 text-center text-sm text-text-secondary">
                    <img src="{{ asset('images/auto-mercy-logo.webp') }}" width="72" height="72" alt="" loading="lazy" class="h-16 w-16 object-contain opacity-70">
                    Vehicle imagery is being prepared
                </span>
            @endif
            <span class="absolute left-4 top-4 z-10 rounded-badge {{ $statusClass }} px-3 py-1.5 text-xs font-bold uppercase tracking-label text-pure-white">{{ $statusLabel }}</span>
        </a>
        <button type="button" class="absolute right-4 top-4 inline-flex h-10 w-10 items-center justify-center rounded-full bg-pure-white text-text-secondary shadow-card transition-[transform,color,box-shadow] hover:scale-105 hover:text-mercy-red hover:shadow-card-hover" data-save-vehicle="{{ $car->slug }}" aria-label="Save {{ $accessibleName }}" aria-pressed="false">
            <svg class="h-[1.125rem] w-[1.125rem]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true" data-save-icon-outline><path stroke-linecap="round" stroke-linejoin="round" d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78L12 21.23l8.84-8.84a5.5 5.5 0 0 0 0-7.78Z"/></svg>
            <svg class="hidden h-[1.125rem] w-[1.125rem] text-mercy-red" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" data-save-icon-filled><path d="M12 21.23 3.16 12.39a5.5 5.5 0 0 1 7.78-7.78L12 5.67l1.06-1.06a5.5 5.5 0 0 1 7.78 7.78L12 21.23Z"/></svg>
        </button>
    </div>

    <div class="flex flex-1 flex-col p-4 sm:p-5 sm:pt-4">
        <p class="text-xs font-semibold uppercase tracking-label text-mercy-red">{{ $car->bodyType?->name ?? 'Vehicle' }}</p>
        <h3 class="vehicle-card-title mt-1.5 text-vehicle-name text-carbon">
            <a href="{{ route('cars.show', $car) }}" class="transition-colors hover:text-mercy-red" aria-label="View details for {{ $accessibleName }}" title="{{ $accessibleName }}">{{ $vehicleName }}</a>
        </h3>
        <p class="mt-0.5 min-h-4 text-xs text-text-secondary/80">{{ $car->trim }}</p>

        @if ($mileage || $car->transmission || $car->fuel_type)
            <dl class="mt-2.5 grid grid-cols-[1.15fr_1fr_0.75fr] items-center gap-1.5 py-2 text-[0.6875rem] font-medium text-text-secondary sm:gap-2 sm:text-xs">
                @if ($mileage)
                    <div class="flex items-center gap-1 whitespace-nowrap" title="Mileage: {{ $mileage }}">
                        <dt class="sr-only">Mileage</dt>
                        <svg class="h-3.5 w-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4 17a8 8 0 1 1 16 0H4Z"/><path stroke-linecap="round" d="m12 14 3-3M7 17v-1m10 1v-1"/></svg>
                        <dd>{{ $mileage }}</dd>
                    </div>
                @endif
                @if ($car->transmission)
                    <div class="flex items-center gap-1 whitespace-nowrap" title="Transmission: {{ $car->transmission->label() }}">
                        <dt class="sr-only">Transmission</dt>
                        <svg class="h-3.5 w-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><circle cx="7" cy="5" r="2"/><circle cx="17" cy="5" r="2"/><circle cx="7" cy="19" r="2"/><circle cx="17" cy="19" r="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M7 7v10m10-10v4H7m10 0v6"/></svg>
                        <dd>{{ $car->transmission->label() }}</dd>
                    </div>
                @endif
                @if ($car->fuel_type)
                    <div class="flex items-center gap-1 whitespace-nowrap" title="Fuel: {{ $car->fuel_type->label() }}">
                        <dt class="sr-only">Fuel</dt>
                        <svg class="h-3.5 w-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 21V4a1 1 0 0 1 1-1h7a1 1 0 0 1 1 1v17M5 21h12M8 7h5v4H8zM15 8h2l2 2v7a2 2 0 0 0 2 2V9l-2-2"/></svg>
                        <dd>{{ $car->fuel_type->label() }}</dd>
                    </div>
                @endif
            </dl>
        @endif

        <div class="mt-auto flex items-center justify-between gap-3 pt-3">
            <p class="text-lg font-bold tracking-tight text-mercy-red sm:text-xl">₦{{ number_format((float) $car->price_amount) }}</p>
            <a href="{{ route('cars.show', $car) }}" class="inline-flex min-h-10 shrink-0 items-center justify-center rounded-button border border-carbon px-3.5 py-2 text-sm font-semibold text-carbon transition-colors hover:bg-carbon hover:text-pure-white" aria-label="View details for {{ $accessibleName }}">View Details</a>
        </div>
    </div>
</article>
