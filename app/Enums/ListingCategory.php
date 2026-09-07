<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ListingCategory: string implements HasColor, HasLabel
{
    case BrandNew = 'brand_new';
    case ForeignUsed = 'foreign_used';
    case PreOrder = 'pre_order';

    public function label(): string
    {
        return match ($this) {
            self::BrandNew => 'Brand New',
            self::ForeignUsed => 'Foreign Used',
            self::PreOrder => 'Pre-Order',
        };
    }

    public function getLabel(): ?string
    {
        return $this->label();
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::BrandNew => 'primary',
            self::ForeignUsed => 'success',
            self::PreOrder => 'warning',
        };
    }
}
