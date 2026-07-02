<?php

namespace App\Enums;

enum MembershipApplicationStatus: string
{
    case Pending = 'pending';
    case UnderReview = 'under_review';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Cancelled = 'cancelled';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Menunggu',
            self::UnderReview => 'Dalam Proses',
            self::Approved => 'Diluluskan',
            self::Rejected => 'Ditolak',
            self::Cancelled => 'Dibatalkan',
        };
    }
}
