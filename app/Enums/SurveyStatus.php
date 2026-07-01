<?php

namespace App\Enums;

enum SurveyStatus: string
{
    case Draft = 'draft';
    case Published = 'published';
    case Closed = 'closed';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
