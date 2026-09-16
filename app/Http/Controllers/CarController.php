<?php

namespace App\Http\Controllers;

use App\Enums\CarStatus;
use App\Enums\FuelType;
use App\Enums\ListingCategory;
use App\Enums\TransmissionType;
use App\Http\Requests\InventoryFilterRequest;
use App\Models\Car;
use App\Queries\CarInventoryQuery;
use App\Support\AttributionParameters;
use App\Support\SeoStructuredData;
use App\Support\SeoUrl;
use BackedEnum;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Gate;

class CarController extends Controller
{
    public function index(
        InventoryFilterRequest $request,
        CarInventoryQuery $inventoryQuery,
        AttributionParameters $attribution,
        SeoUrl $urls,
    ): View|RedirectResponse {
        $filters = $request->validated();
        $normalizedFilters = $inventoryQuery->normalize($filters);

        if ($inventoryQuery->shouldRedirectToCanonicalQuery($request->query(), AttributionParameters::ALLOWED)
            || $attribution->hasUnsupported($request->query(), CarInventoryQuery::PARAMETERS)) {
            return redirect()->to(route('cars.index', [
                ...$normalizedFilters,
                ...$attribution->from($request->query()),
            ], false), 301);
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
        $canonicalQuery = $hasFilteredQuery || isset($normalizedFilters['page']) ? $normalizedFilters : [];
        $hasPublicInventory = $cars->isNotEmpty() || (! $hasFilterErrors && $inventoryQuery->hasPublicInventory());

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
            'hasPublicInventory' => $hasPublicInventory,
            'robots' => $hasFilteredQuery || $hasFilterErrors || ! $hasPublicInventory ? 'noindex,follow' : 'index,follow',
            'canonical' => $urls->route('cars.index', [], $canonicalQuery),
        ]);
    }

    public function show(Car $car, SeoUrl $urls, SeoStructuredData $structuredData): View
    {
        abort_if($car->status === CarStatus::Archived, 410);
        abort_if($car->status === CarStatus::Draft, 404);

        return view('cars.show', $this->vehicleViewData($car, false, $urls, $structuredData));
    }

    public function preview(Car $car, SeoUrl $urls, SeoStructuredData $structuredData): View
    {
        Gate::authorize('view', $car);

        return view('cars.show', $this->vehicleViewData($car, true, $urls, $structuredData));
    }

    private function relatedCars(Car $car): Collection
    {
        return Car::query()->where('status', CarStatus::Available)->whereKeyNot($car->getKey())->with('coverImage')
            ->orderByRaw('CASE WHEN body_type = ? THEN 0 WHEN make = ? THEN 1 ELSE 2 END', [$car->body_type ?? '', $car->make])
            ->orderByRaw('ABS(CAST(price_amount AS SIGNED) - ?)', [(int) $car->price_amount])
            ->latest()->limit(3)->get();
    }

    /** @return array<string, mixed> */
    private function vehicleViewData(Car $car, bool $isPreview, SeoUrl $urls, SeoStructuredData $structuredData): array
    {
        $car->load(['coverImage', 'images', 'features']);
        $canonical = $urls->route('cars.show', $car);
        $images = $car->images
            ->map(fn ($image): string => $urls->absolute($image->variantUrl('large')))
            ->values()
            ->all();
        $shareImage = $car->coverImage
            ? $urls->absolute($car->coverImage->variantUrl('large'))
            : ($images[0] ?? $urls->absolute('/images/auto-mercy-hero.webp'));
        $breadcrumbs = [
            ['name' => 'Home', 'url' => $urls->route('home')],
            ['name' => 'Cars', 'url' => $urls->route('cars.index')],
            ['name' => $car->display_name, 'url' => $canonical],
        ];

        return [
            'car' => $car,
            'business' => (array) config('automercy.business'),
            'isPreview' => $isPreview,
            'relatedCars' => $this->relatedCars($car),
            'relatedLandings' => $this->publishedLandingLinks($car, $urls),
            'canonical' => $canonical,
            'pageTitle' => $this->vehicleTitle($car),
            'pageDescription' => $this->vehicleDescription($car),
            'plainDescription' => $structuredData->plainText($car->description),
            'structuredData' => $isPreview ? null : $structuredData->vehicle($car, $images, $breadcrumbs),
            'breadcrumbs' => $breadcrumbs,
            'shareImage' => $shareImage,
            'shareImageAlt' => $car->coverImage?->alt_text ?: $car->display_name.' at Auto Mercy',
        ];
    }

    private function vehicleTitle(Car $car): string
    {
        return match ($car->status) {
            CarStatus::Sold => 'Sold: '.$car->display_name.' | Auto Mercy',
            CarStatus::Reserved => 'Reserved: '.$car->display_name.' | Auto Mercy',
            default => $car->display_name.' for Sale in Lagos | Auto Mercy',
        };
    }

    private function vehicleDescription(Car $car): string
    {
        $facts = [$car->listing_category->label()];

        if (filled($car->body_type)) {
            $facts[] = $car->body_type;
        }

        $facts[] = 'priced at ₦'.number_format((int) $car->price_amount);

        if ($car->mileage !== null) {
            $facts[] = number_format((int) $car->mileage).' '.($car->mileage_unit?->label() ?? 'mileage');
        }

        $summary = $car->display_name.' is a '.implode(', ', $facts).'. ';

        return $summary.match ($car->status) {
            CarStatus::Sold => 'This vehicle is sold. Contact Auto Mercy to ask about similar available cars.',
            CarStatus::Reserved => 'This vehicle is currently reserved. Contact Auto Mercy to confirm its status or ask about alternatives.',
            default => 'Contact Auto Mercy to confirm availability and arrange an inspection in Lagos.',
        };
    }

    /** @return list<array{name: string, url: string}> */
    private function publishedLandingLinks(Car $car, SeoUrl $urls): array
    {
        return collect((array) config('automercy.seo.landings'))
            ->filter(fn (array $landing): bool => (bool) ($landing['published'] ?? false))
            ->filter(function (array $landing) use ($car): bool {
                return collect((array) $landing['filter'])->every(
                    function (string $value, string $field) use ($car): bool {
                        $attribute = $car->getAttribute($field);

                        return mb_strtolower((string) ($attribute instanceof BackedEnum ? $attribute->value : $attribute)) === mb_strtolower($value);
                    },
                );
            })
            ->filter(function (array $landing): bool {
                $query = Car::query()->activeInventory();

                foreach ((array) $landing['filter'] as $field => $value) {
                    $query->where($field, $value);
                }

                return $query->exists();
            })
            ->map(function (array $landing, string $key) use ($urls): array {
                [$type, $slug] = explode('/', $key, 2);

                return ['name' => $landing['heading'], 'url' => $urls->route('cars.landings.show', compact('type', 'slug'))];
            })
            ->values()
            ->all();
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
