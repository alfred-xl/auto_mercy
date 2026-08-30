<?php

namespace App\Queries;

use App\Enums\CarStatus;
use App\Models\Car;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class CarInventoryQuery
{
    public const PARAMETERS = ['q', 'make', 'model', 'body_type', 'year_min', 'year_max', 'price_min', 'price_max', 'transmission', 'fuel_type', 'mileage_min', 'mileage_max', 'car_stand', 'availability', 'sort', 'page'];

    public const SORT_OPTIONS = [
        'latest' => 'Latest',
        'price_asc' => 'Price: Low to High',
        'price_desc' => 'Price: High to Low',
        'year_desc' => 'Year: Newest First',
        'mileage_asc' => 'Mileage: Lowest First',
    ];

    private const NORMALIZED_MILEAGE_SQL = "CASE WHEN mileage_unit = 'mi' THEN mileage * 1.609344 ELSE mileage END";

    /** @param array<string, mixed> $filters */
    public function paginate(array $filters): LengthAwarePaginator
    {
        $query = Car::query()
            ->select([
                'id', 'slug', 'status', 'published_at', 'year', 'trim', 'price_amount',
                'mileage', 'mileage_unit', 'transmission', 'fuel_type', 'make_id',
                'car_model_id', 'body_type_id', 'car_stand_id', 'primary_image_id',
            ])
            ->with([
                'make:id,name',
                'carModel:id,name',
                'bodyType:id,name',
                'carStand:id,name',
                'primaryImage:id,disk,path,alt_text,width,height',
            ]);

        $this->applyVisibility($query, $filters);
        $this->applyFilters($query, $filters);
        $this->applySort($query, (string) ($filters['sort'] ?? 'latest'));

        return $query->paginate(12)->appends($this->without($filters, 'page'));
    }

    public function hasPublicInventory(): bool
    {
        return $this->constrainToPublicInventory(Car::query())->exists();
    }

    public function constrainToPublicInventory(Builder $query): Builder
    {
        return $query
            ->whereIn('status', [CarStatus::Available, CarStatus::Reserved])
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    /** @param array<string, mixed> $filters */
    public function normalize(array $filters): array
    {
        $normalized = [];

        foreach (self::PARAMETERS as $parameter) {
            $value = $filters[$parameter] ?? null;

            if (is_string($value)) {
                $value = trim($value);
            }

            if ($value === null || $value === '') {
                continue;
            }

            if (($parameter === 'availability' && $value === 'available')
                || ($parameter === 'sort' && $value === 'latest')
                || ($parameter === 'page' && (int) $value <= 1)) {
                continue;
            }

            $normalized[$parameter] = in_array($parameter, ['year_min', 'year_max', 'price_min', 'price_max', 'mileage_min', 'mileage_max', 'page'], true)
                ? (string) (int) $value
                : $value;
        }

        return $normalized;
    }

    /** @param array<string, mixed> $filters */
    public function shouldRedirectToCanonicalQuery(array $filters): bool
    {
        if (array_diff(array_keys($filters), self::PARAMETERS) !== []) {
            return true;
        }

        if (($filters['availability'] ?? null) === 'available'
            || ($filters['sort'] ?? null) === 'latest'
            || (isset($filters['page']) && (int) $filters['page'] === 1)) {
            return true;
        }

        return isset($filters['q']) && $filters['q'] !== trim((string) $filters['q']);
    }

    /** @param array<string, mixed> $filters */
    public function hasActiveFilters(array $filters): bool
    {
        return collect($this->normalize($filters))->except(['sort', 'page'])->isNotEmpty();
    }

    /**
     * @param  array<string, mixed>  $filters
     * @param  string|array<int, string>  $parameters
     * @return array<string, mixed>
     */
    public function without(array $filters, string|array $parameters): array
    {
        return collect($this->normalize($filters))
            ->except([...(array) $parameters, 'page'])
            ->all();
    }

    /** @param array<string, mixed> $filters */
    private function applyVisibility(Builder $query, array $filters): void
    {
        $status = ($filters['availability'] ?? 'available') === 'reserved'
            ? CarStatus::Reserved
            : CarStatus::Available;

        $query
            ->where('status', $status)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    /** @param array<string, mixed> $filters */
    private function applyFilters(Builder $query, array $filters): void
    {
        $query
            ->when(filled($filters['q'] ?? null), function (Builder $query) use ($filters): void {
                $search = trim((string) $filters['q']);
                $escapedSearch = addcslashes($search, '\\%_');

                $query->where(function (Builder $query) use ($search, $escapedSearch): void {
                    $query
                        ->where('stock_number', 'like', $escapedSearch.'%')
                        ->orWhere('trim', 'like', '%'.$escapedSearch.'%')
                        ->orWhereHas('make', fn (Builder $query) => $query->where('name', 'like', '%'.$escapedSearch.'%'))
                        ->orWhereHas('carModel', fn (Builder $query) => $query->where('name', 'like', '%'.$escapedSearch.'%'));

                    if (preg_match('/^\d{4}$/', $search) === 1) {
                        $query->orWhere('year', (int) $search);
                    }
                });
            })
            ->when($filters['make'] ?? null, fn (Builder $query, string $slug) => $query->whereHas('make', fn (Builder $query) => $query->where('slug', $slug)))
            ->when($filters['model'] ?? null, fn (Builder $query, string $slug) => $query->whereHas('carModel', fn (Builder $query) => $query->where('slug', $slug)))
            ->when($filters['body_type'] ?? null, fn (Builder $query, string $slug) => $query->whereHas('bodyType', fn (Builder $query) => $query->where('slug', $slug)))
            ->when($filters['year_min'] ?? null, fn (Builder $query, int|string $year) => $query->where('year', '>=', (int) $year))
            ->when($filters['year_max'] ?? null, fn (Builder $query, int|string $year) => $query->where('year', '<=', (int) $year))
            ->when($filters['price_min'] ?? null, fn (Builder $query, int|string $price) => $query->where('price_amount', '>=', (int) $price))
            ->when($filters['price_max'] ?? null, fn (Builder $query, int|string $price) => $query->where('price_amount', '<=', (int) $price))
            ->when($filters['transmission'] ?? null, fn (Builder $query, string $transmission) => $query->where('transmission', $transmission))
            ->when($filters['fuel_type'] ?? null, fn (Builder $query, string $fuelType) => $query->where('fuel_type', $fuelType))
            ->when($filters['mileage_min'] ?? null, fn (Builder $query, int|string $mileage) => $query->whereRaw(self::NORMALIZED_MILEAGE_SQL.' >= ?', [(int) $mileage]))
            ->when($filters['mileage_max'] ?? null, fn (Builder $query, int|string $mileage) => $query->whereRaw(self::NORMALIZED_MILEAGE_SQL.' <= ?', [(int) $mileage]))
            ->when($filters['car_stand'] ?? null, fn (Builder $query, string $slug) => $query->whereHas('carStand', fn (Builder $query) => $query->where('slug', $slug)));
    }

    private function applySort(Builder $query, string $sort): void
    {
        match ($sort) {
            'price_asc' => $query->orderBy('price_amount'),
            'price_desc' => $query->orderByDesc('price_amount'),
            'year_desc' => $query->orderByDesc('year'),
            'mileage_asc' => $query->orderByRaw(self::NORMALIZED_MILEAGE_SQL.' ASC'),
            default => $query->orderByDesc('published_at'),
        };

        if ($sort !== 'latest') {
            $query->orderByDesc('published_at');
        }

        $query->orderByDesc('id');
    }
}
