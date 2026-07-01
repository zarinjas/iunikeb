<?php

namespace App\Models;

use App\Enums\AnnouncementAudience;
use App\Enums\AnnouncementPriority;
use App\Enums\AnnouncementStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Announcement extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'cooperative_id',
        'title',
        'slug',
        'summary',
        'content',
        'image_path',
        'audience',
        'status',
        'is_pinned',
        'priority',
        'published_at',
        'expires_at',
        'send_via',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
        'published_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function cooperative(): BelongsTo
    {
        return $this->belongsTo(Cooperative::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function targetedUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'announcement_user')
            ->withPivot(['read_at', 'target_type', 'notification_id'])
            ->withTimestamps();
    }

    public function scopeForCooperative(Builder $query, ?int $cooperativeId): Builder
    {
        return $query->when($cooperativeId, fn (Builder $query) => $query->where('cooperative_id', $cooperativeId));
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', AnnouncementStatus::Published->value);
    }

    public function scopeForAudience(Builder $query, string $audience): Builder
    {
        return $query->where('audience', $audience);
    }

    public function scopePinned(Builder $query): Builder
    {
        return $query->where('is_pinned', true);
    }

    public function scopeNotExpired(Builder $query): Builder
    {
        return $query->where(function (Builder $query) {
            $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
        });
    }

    public function scopeForMember(Builder $query, User $member): Builder
    {
        return $query->where(function (Builder $query) use ($member) {
            $query->where('audience', AnnouncementAudience::Members->value)
                ->orWhere('audience', AnnouncementAudience::Public->value)
                ->orWhereHas('targetedUsers', fn ($q) => $q->where('user_id', $member->id));
        });
    }

    public function isPublished(): bool
    {
        return $this->status === AnnouncementStatus::Published->value;
    }

    public function isDraft(): bool
    {
        return $this->status === AnnouncementStatus::Draft->value;
    }

    public function isArchived(): bool
    {
        return $this->status === AnnouncementStatus::Archived->value;
    }

    public function hasEmail(): bool
    {
        return str_contains((string) $this->send_via, 'email');
    }

    public function hasInApp(): bool
    {
        return str_contains((string) $this->send_via, 'in_app');
    }
}
