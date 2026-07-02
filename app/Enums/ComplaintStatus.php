<?php

namespace App\Enums;

enum ComplaintStatus: string
{
    case Open = 'open';
    case InProgress = 'in_progress';
    case Resolved = 'resolved';
    case Closed = 'closed';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match ($this) {
            self::Open => 'Terbuka',
            self::InProgress => 'Dalam Tindakan',
            self::Resolved => 'Selesai',
            self::Closed => 'Ditutup',
        };
    }
}
