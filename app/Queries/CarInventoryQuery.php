<?php

namespace App\Queries;

use App\Enums\CarStatus;
use App\Models\Car;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class CarInventoryQuery
{
    public const PARAMETERS = ['q', 'listing_category', 'make', 'model', 'body_type', 'year_min', 'year_max', 'price_min', 'price_max', 'transmission', 'fuel_type', 'availability', 'sort', 'page'];

    public const SORT_OPTIONS = [
        'latest' => 'Latest',
        'price_asc' => 'Price: Low to High',
        'price_desc' => 'Price: High to Low',
        'year_desc' => 'Year: Newest First',
    ];

    public function paginate(array $filters): LengthAwarePaginator
    {
        $query = Car::query()->with('coverImage');
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
        return $query->whereIn('status', [CarStatus::Available, CarStatus::Reserved]);
    }

    public function normalize(array $filters): array
    {
        return collect($filters)
            ->only(self::PARAMETERS)
            ->map(fn ($value) => is_string($value) ? trim($value) : $value)
            ->reject(fn ($value) => $value === null || $value === '')
            ->reject(fn ($value, string $key): bool => ($key === 'sort' && $value === 'latest') || ($key === 'page' && (int) $value <= 1))
            ->all();
    }

    public function shouldRedirectToCanonicalQuery(array $filters): bool
    {
        return array_diff(array_keys($filters), self::PARAMETERS) !== []
            || (($filters['sort'] ?? null) === 'latest')
            || (isset($filters['page']) && (int) $filters['page'] <= 1)
            || (isset($filters['q']) && $filters['q'] !== trim((string) $filters['q']));
    }

    public function hasActiveFilters(array $filters): bool
    {
        return collect($this->normalize($filters))->except(['sort', 'page'])->isNotEmpty();
    }

    public function without(array $filters, string|array $parameters): array
    {
        return collect($this->normalize($filters))->except([...(array) $parameters, 'page'])->all();
    }

    private function applyVisibility(Builder $query, array $filters): void
    {
        $query->whereIn('status', [CarStatus::Available, CarStatus::Reserved])
            ->when($filters['availability'] ?? null, fn (Builder $query, string $status) => $query->where('status', $status));
    }

    private function applyFilters(Builder $query, array $filters): void
    {
        $query
            ->when(filled($filters['q'] ?? null), function (Builder $query) use ($filters): void {
                $search = addcslashes(trim((string) $filters['q']), '\\%_');
                $query->where(fn (Builder $query) => $query->where('stock_number', 'like', "%{$search}%")->orWhere('make', 'like', "%{$search}%")->orWhere('model', 'like', "%{$search}%")->orWhere('trim', 'like', "%{$search}%"));
            })
            ->when($filters['listing_category'] ?? null, fn (Builder $query, string $category) => $query->where('listing_category', $category))
            ->when($filters['make'] ?? null, fn (Builder $query, string $make) => $query->where('make', $make))
            ->when($filters['model'] ?? null, fn (Builder $query, string $model) => $query->where('model', $model))
            ->when($filters['body_type'] ?? null, fn (Builder $query, string $bodyType) => $query->where('body_type', $bodyType))
            ->when($filters['year_min'] ?? null, fn (Builder $query, $year) => $query->where('year', '>=', (int) $year))
            ->when($filters['year_max'] ?? null, fn (Builder $query, $year) => $query->where('year', '<=', (int) $year))
            ->when($filters['price_min'] ?? null, fn (Builder $query, $price) => $query->where('price_amount', '>=', (int) $price))
            ->when($filters['price_max'] ?? null, fn (Builder $query, $price) => $query->where('price_amount', '<=', (int) $price))
            ->when($filters['transmission'] ?? null, fn (Builder $query, string $transmission) => $query->where('transmission', $transmission))
            ->when($filters['fuel_type'] ?? null, fn (Builder $query, string $fuelType) => $query->where('fuel_type', $fuelType));
    }

    private function applySort(Builder $query, string $sort): void
    {
        match ($sort) {
            'price_asc' => $query->orderBy('price_amount'),
            'price_desc' => $query->orderByDesc('price_amount'),
            'year_desc' => $query->orderByDesc('year'),
            default => $query->orderByDesc('created_at'),
        };

        $query->orderByDesc('id');
    }
}
