<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SurveyResponse extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'survey_id',
        'survey_option_id',
        'member_id',
    ];

    public function survey(): BelongsTo
    {
        return $this->belongsTo(Survey::class);
    }

    public function option(): BelongsTo
    {
        return $this->belongsTo(SurveyOption::class, 'survey_option_id');
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }
}
