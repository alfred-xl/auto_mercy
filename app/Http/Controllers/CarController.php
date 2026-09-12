<?php

namespace App\Http\Controllers;

use App\Enums\CarStatus;
use App\Enums\FuelType;
use App\Enums\ListingCategory;
use App\Enums\TransmissionType;
use App\Http\Requests\InventoryFilterRequest;
use App\Models\Car;
use App\Queries\CarInventoryQuery;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Gate;

class CarController extends Controller
{
    public function index(InventoryFilterRequest $request, CarInventoryQuery $inventoryQuery): View|RedirectResponse
    {
        $filters = $request->validated();
        $normalizedFilters = $inventoryQuery->normalize($filters);

        if ($inventoryQuery->shouldRedirectToCanonicalQuery($request->query())) {
            return redirect()->route('cars.index', $normalizedFilters, 301);
        }

        $hasFilterErrors = $request->session()->has('errors');
        $cars = $hasFilterErrors ? new LengthAwarePaginator([], 0, 12, 1, ['path' => route('cars.index')]) : $inventoryQuery->paginate($filters);
        abort_if($cars->currentPage() > 1 && $cars->isEmpty(), 404);
        $activeCars = Car::query()->activeInventory();
        $makes = (clone $activeCars)->distinct()->orderBy('make')->pluck('make');
        $models = (clone $activeCars)->get(['make', 'model'])->unique(fn (Car $car): string => $car->make.'|'.$car->model)->sortBy('model')->values();
        $bodyTypes = (clone $activeCars)->whereNotNull('body_type')->distinct()->orderBy('body_type')->pluck('body_type');
        $activeFilters = $this->activeFilterChips($filters, $inventoryQuery);
        $hasFilteredQuery = $inventoryQuery->hasActiveFilters($filters) || ($filters['sort'] ?? 'latest') !== 'latest';

        return view('cars.index', [
            'cars' => $cars,
            'filters' => $filters,
            'normalizedFilters' => $normalizedFilters,
            'makes' => $makes,
            'models' => $models,
            'bodyTypes' => $bodyTypes,
            'categories' => ListingCategory::cases(),
            'transmissions' => TransmissionType::cases(),
            'fuels' => FuelType::cases(),
            'sortOptions' => CarInventoryQuery::SORT_OPTIONS,
            'activeFilters' => $activeFilters,
            'activeFilterCount' => count($activeFilters),
            'hasFilterErrors' => $hasFilterErrors,
            'hasPublicInventory' => $cars->isNotEmpty() || (! $hasFilterErrors && $inventoryQuery->hasPublicInventory()),
            'robots' => $hasFilteredQuery || $hasFilterErrors ? 'noindex,follow' : 'index,follow',
            'canonical' => route('cars.index'),
        ]);
    }

    public function show(Car $car): View
    {
        abort_if($car->status === CarStatus::Archived, 410);
        abort_if($car->status === CarStatus::Draft, 404);
        $car->load(['coverImage', 'images', 'features']);

        return view('cars.show', ['car' => $car, 'business' => (array) config('automercy.business'), 'relatedCars' => $this->relatedCars($car)]);
    }

    public function preview(Car $car): View
    {
        Gate::authorize('view', $car);
        $car->load(['coverImage', 'images', 'features']);

        return view('cars.show', ['car' => $car, 'business' => (array) config('automercy.business'), 'isPreview' => true, 'relatedCars' => $this->relatedCars($car)]);
    }

    private function relatedCars(Car $car)
    {
        return Car::query()->activeInventory()->whereKeyNot($car->getKey())->with('coverImage')
            ->orderByRaw('CASE WHEN body_type = ? THEN 0 WHEN make = ? THEN 1 ELSE 2 END', [$car->body_type ?? '', $car->make])
            ->orderByRaw('ABS(CAST(price_amount AS SIGNED) - ?)', [(int) $car->price_amount])
            ->latest()->limit(3)->get();
    }

    private function activeFilterChips(array $filters, CarInventoryQuery $inventoryQuery): array
    {
        $labels = [
            'q' => fn ($value) => 'Search: “'.$value.'”',
            'listing_category' => fn ($value) => ListingCategory::tryFrom($value)?->label(),
            'make' => fn ($value) => $value,
            'model' => fn ($value) => $value,
            'body_type' => fn ($value) => $value,
            'year_min' => fn ($value) => 'From '.$value,
            'year_max' => fn ($value) => 'Up to '.$value,
            'price_min' => fn ($value) => 'From ₦'.number_format((int) $value),
            'price_max' => fn ($value) => 'Up to ₦'.number_format((int) $value),
            'transmission' => fn ($value) => TransmissionType::tryFrom($value)?->label(),
            'fuel_type' => fn ($value) => FuelType::tryFrom($value)?->label(),
            'availability' => fn ($value) => ucfirst($value),
        ];

        return collect($filters)->filter()->map(function ($value, string $key) use ($labels, $filters, $inventoryQuery): ?array {
            if (! isset($labels[$key])) {
                return null;
            }

            return ['key' => $key, 'label' => $labels[$key]($value), 'url' => route('cars.index', $inventoryQuery->without($filters, $key))];
        })->filter()->values()->all();
    }
}
