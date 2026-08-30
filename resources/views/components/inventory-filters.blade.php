@props([
    'filters',
    'makes',
    'models',
    'bodyTypes',
    'stands',
    'transmissions',
    'fuels',
    'prefix' => 'inventory',
    'mobile' => false,
])

@php
    $value = fn (string $key, mixed $default = '') => old($key, $filters[$key] ?? $default);
    $selectedMake = (string) $value('make');
    $makeSlugs = $makes->pluck('slug', 'id');
    $moreFiltersOpen = collect(['fuel_type', 'mileage_min', 'mileage_max'])->contains(fn (string $key) => filled($value($key)))
        || $value('availability', 'available') === 'reserved';
@endphp

<form action="{{ route('cars.index') }}" method="GET" {{ $attributes->merge(['class' => 'grid gap-6']) }} data-inventory-filter-form>
    @if ($errors->any())
        <div class="rounded-input border border-mercy-red/35 bg-mercy-red/5 p-4 text-sm text-carbon" role="alert">
            <p class="font-semibold">Check the highlighted filters.</p>
            <ul class="mt-2 list-disc space-y-1 pl-5 text-text-secondary">
                @foreach ($errors->all() as $message)
                    <li>{{ $message }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div>
        <label class="field-label" for="{{ $prefix }}-q">Keyword</label>
        <input id="{{ $prefix }}-q" name="q" type="search" maxlength="100" value="{{ $value('q') }}" class="field-control" placeholder="Make, model, year or stock number" autocomplete="off" @error('q') aria-invalid="true" aria-describedby="{{ $prefix }}-q-error" @enderror>
        @error('q')<p id="{{ $prefix }}-q-error" class="mt-2 text-sm font-medium text-mercy-red">{{ $message }}</p>@enderror
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-1">
        <div>
            <label class="field-label" for="{{ $prefix }}-make">Make</label>
            <select id="{{ $prefix }}-make" name="make" class="field-control" data-inventory-make @error('make') aria-invalid="true" aria-describedby="{{ $prefix }}-make-error" @enderror>
                <option value="">All makes</option>
                @foreach ($makes as $make)
                    <option value="{{ $make->slug }}" @selected((string) $value('make') === $make->slug)>{{ $make->name }}</option>
                @endforeach
            </select>
            @error('make')<p id="{{ $prefix }}-make-error" class="mt-2 text-sm font-medium text-mercy-red">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="field-label" for="{{ $prefix }}-model">Model</label>
            <select id="{{ $prefix }}-model" name="model" class="field-control" data-inventory-model @disabled($selectedMake === '') @error('model') aria-invalid="true" aria-describedby="{{ $prefix }}-model-error" @enderror>
                <option value="">All models</option>
                @foreach ($models as $model)
                    @php($modelMakeSlug = $makeSlugs->get($model->make_id))
                    <option value="{{ $model->slug }}" data-make="{{ $modelMakeSlug }}" @selected((string) $value('model') === $model->slug && $selectedMake === $modelMakeSlug) @disabled($selectedMake === '' || $selectedMake !== $modelMakeSlug) @if ($selectedMake === '' || $selectedMake !== $modelMakeSlug) hidden @endif>{{ $model->name }}</option>
                @endforeach
            </select>
            @error('model')<p id="{{ $prefix }}-model-error" class="mt-2 text-sm font-medium text-mercy-red">{{ $message }}</p>@enderror
            <p class="sr-only" aria-live="polite" data-model-announcement></p>
        </div>
    </div>

    <fieldset>
        <legend class="field-label">Year</legend>
        <div class="grid grid-cols-2 gap-3">
            <div><label class="sr-only" for="{{ $prefix }}-year-min">Minimum year</label><input id="{{ $prefix }}-year-min" name="year_min" type="number" inputmode="numeric" min="1900" max="{{ now()->year + 1 }}" step="1" value="{{ $value('year_min') }}" class="field-control" placeholder="Minimum" @error('year_min') aria-invalid="true" @enderror></div>
            <div><label class="sr-only" for="{{ $prefix }}-year-max">Maximum year</label><input id="{{ $prefix }}-year-max" name="year_max" type="number" inputmode="numeric" min="1900" max="{{ now()->year + 1 }}" step="1" value="{{ $value('year_max') }}" class="field-control" placeholder="Maximum" @error('year_max') aria-invalid="true" aria-describedby="{{ $prefix }}-year-error" @enderror></div>
        </div>
        @error('year_min')<p id="{{ $prefix }}-year-error" class="mt-2 text-sm font-medium text-mercy-red">{{ $message }}</p>@enderror
        @error('year_max')<p id="{{ $prefix }}-year-error" class="mt-2 text-sm font-medium text-mercy-red">{{ $message }}</p>@enderror
    </fieldset>

    <fieldset>
        <legend class="field-label">Price range</legend>
        <p class="mb-2 text-xs text-text-secondary">Amounts in Nigerian naira (₦)</p>
        <div class="grid grid-cols-2 gap-3">
            <div><label class="sr-only" for="{{ $prefix }}-price-min">Minimum price in naira</label><input id="{{ $prefix }}-price-min" name="price_min" type="number" inputmode="numeric" min="0" step="100000" value="{{ $value('price_min') }}" class="field-control" placeholder="Minimum" @error('price_min') aria-invalid="true" @enderror></div>
            <div><label class="sr-only" for="{{ $prefix }}-price-max">Maximum price in naira</label><input id="{{ $prefix }}-price-max" name="price_max" type="number" inputmode="numeric" min="0" step="100000" value="{{ $value('price_max') }}" class="field-control" placeholder="Maximum" @error('price_max') aria-invalid="true" aria-describedby="{{ $prefix }}-price-error" @enderror></div>
        </div>
        @error('price_min')<p id="{{ $prefix }}-price-error" class="mt-2 text-sm font-medium text-mercy-red">{{ $message }}</p>@enderror
        @error('price_max')<p id="{{ $prefix }}-price-error" class="mt-2 text-sm font-medium text-mercy-red">{{ $message }}</p>@enderror
    </fieldset>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-1">
        <div>
            <label class="field-label" for="{{ $prefix }}-body-type">Body type</label>
            <select id="{{ $prefix }}-body-type" name="body_type" class="field-control" @error('body_type') aria-invalid="true" @enderror><option value="">All body types</option>@foreach ($bodyTypes as $bodyType)<option value="{{ $bodyType->slug }}" @selected((string) $value('body_type') === $bodyType->slug)>{{ $bodyType->name }}</option>@endforeach</select>
            @error('body_type')<p class="mt-2 text-sm font-medium text-mercy-red">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="field-label" for="{{ $prefix }}-transmission">Transmission</label>
            <select id="{{ $prefix }}-transmission" name="transmission" class="field-control" @error('transmission') aria-invalid="true" @enderror><option value="">Any transmission</option>@foreach ($transmissions as $transmission)<option value="{{ $transmission->value }}" @selected((string) $value('transmission') === $transmission->value)>{{ $transmission->label() }}</option>@endforeach</select>
            @error('transmission')<p class="mt-2 text-sm font-medium text-mercy-red">{{ $message }}</p>@enderror
        </div>
    </div>

    <div>
        <label class="field-label" for="{{ $prefix }}-stand">Car stand</label>
        <select id="{{ $prefix }}-stand" name="car_stand" class="field-control" @error('car_stand') aria-invalid="true" @enderror><option value="">Either car stand</option>@foreach ($stands as $stand)<option value="{{ $stand->slug }}" @selected((string) $value('car_stand') === $stand->slug)>{{ $stand->name }}</option>@endforeach</select>
        @error('car_stand')<p class="mt-2 text-sm font-medium text-mercy-red">{{ $message }}</p>@enderror
    </div>

    <details class="border-t border-border-default pt-5" @if ($moreFiltersOpen) open @endif>
        <summary class="cursor-pointer text-sm font-semibold text-carbon marker:text-mercy-red">More Filters</summary>
        <div class="mt-5 grid gap-5">
            <div>
                <label class="field-label" for="{{ $prefix }}-fuel">Fuel type</label>
                <select id="{{ $prefix }}-fuel" name="fuel_type" class="field-control" @error('fuel_type') aria-invalid="true" @enderror><option value="">Any fuel type</option>@foreach ($fuels as $fuel)<option value="{{ $fuel->value }}" @selected((string) $value('fuel_type') === $fuel->value)>{{ $fuel->label() }}</option>@endforeach</select>
                @error('fuel_type')<p class="mt-2 text-sm font-medium text-mercy-red">{{ $message }}</p>@enderror
            </div>
            <fieldset>
                <legend class="field-label">Mileage range</legend>
                <p class="mb-2 text-xs text-text-secondary">Normalized kilometres for filtering</p>
                <div class="grid grid-cols-2 gap-3"><div><label class="sr-only" for="{{ $prefix }}-mileage-min">Minimum mileage</label><input id="{{ $prefix }}-mileage-min" name="mileage_min" type="number" inputmode="numeric" min="0" step="1000" value="{{ $value('mileage_min') }}" class="field-control" placeholder="Minimum" @error('mileage_min') aria-invalid="true" @enderror></div><div><label class="sr-only" for="{{ $prefix }}-mileage-max">Maximum mileage</label><input id="{{ $prefix }}-mileage-max" name="mileage_max" type="number" inputmode="numeric" min="0" step="1000" value="{{ $value('mileage_max') }}" class="field-control" placeholder="Maximum" @error('mileage_max') aria-invalid="true" @enderror></div></div>
                @error('mileage_min')<p class="mt-2 text-sm font-medium text-mercy-red">{{ $message }}</p>@enderror
                @error('mileage_max')<p class="mt-2 text-sm font-medium text-mercy-red">{{ $message }}</p>@enderror
            </fieldset>
            <div>
                <label class="field-label" for="{{ $prefix }}-availability">Availability</label>
                <select id="{{ $prefix }}-availability" name="availability" class="field-control" @error('availability') aria-invalid="true" @enderror><option value="available" @selected((string) $value('availability', 'available') === 'available')>Available</option><option value="reserved" @selected((string) $value('availability', 'available') === 'reserved')>Reserved</option></select>
                @error('availability')<p class="mt-2 text-sm font-medium text-mercy-red">{{ $message }}</p>@enderror
            </div>
        </div>
    </details>

    @if (filled($value('sort')) && $value('sort') !== 'latest')
        <input type="hidden" name="sort" value="{{ $value('sort') }}">
    @endif

    <div class="grid grid-cols-2 gap-3 border-t border-border-default pt-5">
        @if ($mobile)
            <button type="button" class="btn-secondary px-3" data-inventory-filter-cancel>Cancel</button>
        @else
            <button type="reset" class="btn-secondary px-3">Reset changes</button>
        @endif
        <button type="submit" class="btn-primary px-3" data-inventory-filter-submit>Apply Filters</button>
    </div>
</form>
