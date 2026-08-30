<?php

namespace App\Models;

use App\Enums\CarStatus;
use App\Enums\DrivetrainType;
use App\Enums\FuelType;
use App\Enums\MileageUnit;
use App\Enums\TransmissionType;
use App\Enums\VehicleCondition;
use Database\Factories\CarFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['make_id', 'car_model_id', 'body_type_id', 'car_stand_id', 'trim', 'year', 'price_amount', 'currency', 'mileage', 'mileage_unit', 'condition', 'transmission', 'fuel_type', 'drivetrain', 'engine', 'exterior_colour', 'interior_colour', 'description', 'supplemental_specs', 'is_featured', 'video_url', 'meta_title', 'meta_description', 'canonical_override'])]
class Car extends Model
{
    /** @use HasFactory<CarFactory> */
    use HasFactory;

    use SoftDeletes;

    public bool $statusTransitionInProgress = false;

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getDisplayNameAttribute(): string
    {
        $identity = implode(' ', array_filter([
            $this->year,
            $this->make?->name,
            $this->carModel?->name,
            $this->trim,
        ]));

        return trim($identity.' — '.($this->stock_number ?? 'Draft'));
    }

    public function make(): BelongsTo
    {
        return $this->belongsTo(Make::class);
    }

    public function carModel(): BelongsTo
    {
        return $this->belongsTo(CarModel::class);
    }

    public function bodyType(): BelongsTo
    {
        return $this->belongsTo(BodyType::class);
    }

    public function carStand(): BelongsTo
    {
        return $this->belongsTo(CarStand::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(CarImage::class)->orderBy('display_order');
    }

    public function primaryImage(): BelongsTo
    {
        return $this->belongsTo(CarImage::class, 'primary_image_id');
    }

    public function features(): BelongsToMany
    {
        return $this->belongsToMany(Feature::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    #[Scope]
    protected function available(Builder $query): void
    {
        $query
            ->where('status', CarStatus::Available)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    #[Scope]
    protected function featured(Builder $query): void
    {
        $query->where('is_featured', true);
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'status' => CarStatus::class,
            'transmission' => TransmissionType::class,
            'fuel_type' => FuelType::class,
            'drivetrain' => DrivetrainType::class,
            'condition' => VehicleCondition::class,
            'mileage_unit' => MileageUnit::class,
            'supplemental_specs' => 'array',
            'is_featured' => 'boolean',
            'published_at' => 'datetime',
            'reserved_at' => 'datetime',
            'reservation_expires_at' => 'datetime',
            'sold_at' => 'datetime',
            'archived_at' => 'datetime',
        ];
    }
}
