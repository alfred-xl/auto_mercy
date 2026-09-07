<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum LeadSource: string implements HasLabel
{
    case Website = 'website';
    case Phone = 'phone';
    case WhatsApp = 'whatsapp';
    case WalkIn = 'walk_in';
    case Referral = 'referral';
    case SocialMedia = 'social_media';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Website => 'Website',
            self::Phone => 'Phone',
            self::WhatsApp => 'WhatsApp',
            self::WalkIn => 'Walk-in',
            self::Referral => 'Referral',
            self::SocialMedia => 'Social media',
            self::Other => 'Other',
        };
    }

    public function getLabel(): ?string
    {
        return $this->label();
    }
}
