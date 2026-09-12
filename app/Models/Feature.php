<?php

namespace App\Models;

use Database\Factories\FeatureFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

#[Fillable(['name', 'normalized_name'])]
class Feature extends Model
{
    /** @use HasFactory<FeatureFactory> */
    use HasFactory;

    public static function normalizeName(string $name): string
    {
        return Str::lower(Str::squish($name));
    }

    public function cars(): BelongsToMany
    {
        return $this->belongsToMany(Car::class);
    }

    protected static function booted(): void
    {
        static::saving(function (Feature $feature): void {
            $feature->name = Str::squish($feature->name);
            $feature->normalized_name = self::normalizeName($feature->name);
        });
    }
}
