<?php

namespace App\Models;

use App\Enums\SurveyStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Survey extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'cooperative_id',
        'question',
        'description',
        'status',
        'expires_at',
        'total_responses',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function cooperative(): BelongsTo
    {
        return $this->belongsTo(Cooperative::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(SurveyOption::class);
    }

    public function responses(): HasMany
    {
        return $this->hasMany(SurveyResponse::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopeForCooperative(Builder $query, ?int $cooperativeId): Builder
    {
        return $query->when($cooperativeId, fn (Builder $query) => $query->where('cooperative_id', $cooperativeId));
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', SurveyStatus::Published->value);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', SurveyStatus::Published->value)
            ->where(function (Builder $query) {
                $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
            });
    }

    public function scopeClosed(Builder $query): Builder
    {
        return $query->where(function (Builder $query) {
            $query->where('status', SurveyStatus::Closed->value)
                ->orWhere(function (Builder $query) {
                    $query->where('status', SurveyStatus::Published->value)
                        ->where('expires_at', '<=', now());
                });
        });
    }
}
