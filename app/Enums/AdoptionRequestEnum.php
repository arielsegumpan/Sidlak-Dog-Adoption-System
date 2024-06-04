<?php

namespace App\Enums;

use Filament\Support\Contracts\HasIcon;

enum AdoptionRequestEnum: string implements HasIcon
{
    case ADOPTED = 'adopted';
    case AVAILABLE = 'available';
    case PENDING = 'pending';
    case CANCELLED = 'cancelled';

    public function getIcon(): ?string
    {
        return match ($this) {
            self::ADOPTED => 'heroicon-m-check-circle',
            self::AVAILABLE => 'heroicon-m-plus-circle',
            self::PENDING => 'heroicon-m-clock',
            self::CANCELLED => 'heroicon-m-x-circle',
        };
    }
}

