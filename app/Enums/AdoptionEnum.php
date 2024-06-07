<?php

namespace App\Enums;

use Filament\Support\Contracts\HasIcon;

enum AdoptionEnum: string implements HasIcon
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';

    public function getIcon(): ?string
    {
        return match ($this) {
            self::PENDING => 'heroicon-m-check-circle',
            self::APPROVED => 'heroicon-m-plus-circle',
            self::REJECTED => 'heroicon-m-x-circle',
        };
    }
}

