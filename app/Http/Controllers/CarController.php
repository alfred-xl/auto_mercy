<?php

namespace App\Http\Controllers;

use App\Enums\CarStatus;
use App\Enums\FuelType;
use App\Enums\TransmissionType;
use App\Http\Requests\InventoryFilterRequest;
use App\Models\BodyType;
use App\Models\Car;
use App\Models\CarModel;
use App\Models\CarStand;
use App\Models\Make;
use App\Queries\CarInventoryQuery;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

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
        $cars = $hasFilterErrors
            ? new LengthAwarePaginator([], 0, 12, 1, ['path' => route('cars.index')])
            : $inventoryQuery->paginate($filters);

        abort_if($cars->currentPage() > 1 && $cars->isEmpty(), 404);

        $makes = Make::query()->active()->get(['id', 'name', 'slug']);
        $models = CarModel::query()
            ->active()
            ->whereHas('make', fn ($query) => $query->where('is_active', true))
            ->get(['id', 'make_id', 'name', 'slug']);
        $bodyTypes = BodyType::query()
            ->active()
            ->whereHas('cars', fn ($query) => $inventoryQuery->constrainToPublicInventory($query))
            ->get(['id', 'name', 'slug']);
        $stands = CarStand::query()->active()->get(['id', 'name', 'slug']);
        $activeFilters = $this->activeFilterChips($filters, $makes, $models, $bodyTypes, $stands, $inventoryQuery);
        $hasFilteredQuery = $inventoryQuery->hasActiveFilters($filters) || ($filters['sort'] ?? 'latest') !== 'latest';
        $canonical = $hasFilteredQuery
            ? route('cars.index')
            : route('cars.index', collect($normalizedFilters)->only('page')->all());

        return view('cars.index', [
            'cars' => $cars,
            'filters' => $filters,
            'normalizedFilters' => $normalizedFilters,
            'makes' => $makes,
            'models' => $models,
            'bodyTypes' => $bodyTypes,
            'stands' => $stands,
            'transmissions' => TransmissionType::cases(),
            'fuels' => FuelType::cases(),
            'sortOptions' => CarInventoryQuery::SORT_OPTIONS,
            'activeFilters' => $activeFilters,
            'activeFilterCount' => count($activeFilters),
            'hasFilterErrors' => $hasFilterErrors,
            'hasPublicInventory' => $cars->isNotEmpty() || (! $hasFilterErrors && $inventoryQuery->hasPublicInventory()),
            'robots' => $hasFilteredQuery || $hasFilterErrors ? 'noindex,follow' : 'index,follow',
            'canonical' => $canonical,
        ]);
    }

    public function show(Car $car): View
    {
        abort_if($car->status === CarStatus::Archived, 410);
        abort_if(
            $car->status === CarStatus::Draft
            || $car->published_at === null
            || $car->published_at->isFuture(),
            404,
        );

        $car->load(['make', 'carModel', 'bodyType', 'carStand', 'primaryImage', 'images', 'features']);

        return view('cars.show', [
            'car' => $car,
            'business' => (array) config('automercy.business'),
        ]);
    }

    /**
     * @param  array<string, mixed>  $filters
     * @param  Collection<int, Make>  $makes
     * @param  Collection<int, CarModel>  $models
     * @param  Collection<int, BodyType>  $bodyTypes
     * @param  Collection<int, CarStand>  $stands
     * @return array<int, array{key: string, label: string, url: string}>
     */
    private function activeFilterChips(
        array $filters,
        Collection $makes,
        Collection $models,
        Collection $bodyTypes,
        Collection $stands,
        CarInventoryQuery $inventoryQuery,
    ): array {
        $chips = [];
        $addChip = function (string $key, ?string $label, string|array|null $remove = null) use (&$chips, $filters, $inventoryQuery): void {
            if ($label === null || $label === '') {
                return;
            }

            $chips[] = [
                'key' => $key,
                'label' => $label,
                'url' => route('cars.index', $inventoryQuery->without($filters, $remove ?? $key)),
            ];
        };

        $selectedMake = $makes->firstWhere('slug', $filters['make'] ?? null);
        $selectedModel = $models->first(fn (CarModel $model): bool => $model->slug === ($filters['model'] ?? null) && $model->make_id === $selectedMake?->id);

        $addChip('q', filled($filters['q'] ?? null) ? 'Search: “'.trim((string) $filters['q']).'”' : null);
        $addChip('make', $selectedMake?->name, ['make', 'model']);
        $addChip('model', $selectedModel?->name);
        $addChip('body_type', $bodyTypes->firstWhere('slug', $filters['body_type'] ?? null)?->name);
        $addChip('year_min', isset($filters['year_min']) ? 'From '.$filters['year_min'] : null);
        $addChip('year_max', isset($filters['year_max']) ? 'Up to '.$filters['year_max'] : null);
        $addChip('price_min', isset($filters['price_min']) ? 'From ₦'.number_format((int) $filters['price_min']) : null);
        $addChip('price_max', isset($filters['price_max']) ? 'Up to ₦'.number_format((int) $filters['price_max']) : null);
        $addChip('transmission', isset($filters['transmission']) ? TransmissionType::from($filters['transmission'])->label() : null);
        $addChip('fuel_type', isset($filters['fuel_type']) ? FuelType::from($filters['fuel_type'])->label() : null);
        $addChip('mileage_min', isset($filters['mileage_min']) ? 'From '.number_format((int) $filters['mileage_min']).' km' : null);
        $addChip('mileage_max', isset($filters['mileage_max']) ? 'Up to '.number_format((int) $filters['mileage_max']).' km' : null);
        $addChip('car_stand', $stands->firstWhere('slug', $filters['car_stand'] ?? null)?->name);
        $addChip('availability', ($filters['availability'] ?? 'available') === 'reserved' ? 'Reserved' : null);

        return $chips;
    }
}
