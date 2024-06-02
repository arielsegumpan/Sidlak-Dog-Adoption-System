<?php

namespace App\Enums;
use Filament\Support\Contracts\HasLabel;

enum AdoptionRequestEnum: string implements HasLabel
{
    case ADOPTED = 'adopted';
    case AVAILABLE = 'available';
    case PENDING = 'pending';
    case CANCELLED = 'cancelled';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::ADOPTED => 'adopted',
            self::AVAILABLE => 'available',
            self::PENDING => 'pending',
            self::CANCELLED => 'cancelled',
        };
    }
}

