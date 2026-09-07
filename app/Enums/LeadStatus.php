<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum LeadStatus: string implements HasColor, HasLabel
{
    case New = 'new';
    case Contacted = 'contacted';
    case InspectionScheduled = 'inspection_scheduled';
    case Negotiating = 'negotiating';
    case Won = 'won';
    case Lost = 'lost';

    public function label(): string
    {
        return match ($this) {
            self::New => 'New',
            self::Contacted => 'Contacted',
            self::InspectionScheduled => 'Inspection Scheduled',
            self::Negotiating => 'Negotiating',
            self::Won => 'Won',
            self::Lost => 'Lost',
        };
    }

    public function getLabel(): ?string
    {
        return $this->label();
    }

    public function color(): string
    {
        return match ($this) {
            self::New => 'danger',
            self::Contacted => 'info',
            self::InspectionScheduled => 'warning',
            self::Negotiating => 'gray',
            self::Won => 'success',
            self::Lost => 'gray',
        };
    }

    public function getColor(): string|array|null
    {
        return $this->color();
    }

    public function isOpen(): bool
    {
        return ! in_array($this, [self::Won, self::Lost], true);
    }
}
