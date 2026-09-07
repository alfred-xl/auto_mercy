<?php

namespace App\Enums;

enum ImageProcessingStatus: string
{
    case Pending = 'pending';
    case Processing = 'processing';
    case Ready = 'ready';
    case Failed = 'failed';

    public function label(): string
    {
        return ucfirst($this->value);
    }
}
