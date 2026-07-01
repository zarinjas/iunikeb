<?php

namespace App\Enums;

enum AnnouncementPriority: string
{
    case Normal = 'normal';
    case Penting = 'penting';
    case Segera = 'segera';

    public function label(): string
    {
        return match ($this) {
            self::Normal => 'Normal',
            self::Penting => 'Penting',
            self::Segera => 'Segera',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Normal => 'teal',
            self::Penting => 'amber',
            self::Segera => 'red',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
