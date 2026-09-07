<?php

namespace App\Models;

use App\Enums\CarStatus;
use App\Enums\DrivetrainType;
use App\Enums\FuelType;
use App\Enums\ListingCategory;
use App\Enums\MileageUnit;
use App\Enums\TransmissionType;
use Database\Factories\CarFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['listing_category', 'make', 'model', 'trim', 'year', 'body_type', 'price_amount', 'previous_price_amount', 'mileage', 'mileage_unit', 'transmission', 'fuel_type', 'drivetrain', 'engine', 'exterior_colour', 'interior_colour', 'features', 'description', 'is_featured'])]
class Car extends Model
{
    /** @use HasFactory<CarFactory> */
    use HasFactory;

    public bool $statusTransitionInProgress = false;

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getDisplayNameAttribute(): string
    {
        return trim(implode(' ', array_filter([$this->year, $this->make, $this->model, $this->trim])));
    }

    public function images(): HasMany
    {
        return $this->hasMany(CarImage::class)->orderBy('sort_order');
    }

    public function coverImage(): HasOne
    {
        return $this->hasOne(CarImage::class)
            ->where('processing_status', 'ready')
            ->ofMany(['sort_order' => 'min', 'id' => 'min']);
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function activeReservation(): HasOne
    {
        return $this->hasOne(Reservation::class)->where('status', 'active')->latestOfMany();
    }

    #[Scope]
    protected function activeInventory(Builder $query): void
    {
        $query->whereIn('status', [CarStatus::Available, CarStatus::Reserved]);
    }

    #[Scope]
    protected function featured(Builder $query): void
    {
        $query->where('is_featured', true);
    }

    protected function casts(): array
    {
        return [
            'listing_category' => ListingCategory::class,
            'status' => CarStatus::class,
            'mileage_unit' => MileageUnit::class,
            'transmission' => TransmissionType::class,
            'fuel_type' => FuelType::class,
            'drivetrain' => DrivetrainType::class,
            'features' => 'array',
            'is_featured' => 'boolean',
            'sold_at' => 'datetime',
        ];
    }
}
