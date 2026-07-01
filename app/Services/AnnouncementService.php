<?php

namespace App\Services;

use App\Enums\AnnouncementStatus;
use App\Models\Announcement;
use App\Models\User;
use App\Notifications\AnnouncementPublished;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AnnouncementService
{
    public function __construct(
        private readonly AuditLogService $auditLogs,
    ) {}

    public function store(array $data, User $actor): Announcement
    {
        $slug = $data['slug'] ?: str($data['title'])->slug();

        $imagePath = null;
        if (! empty($data['image']) && $data['image']->isValid()) {
            $imagePath = $data['image']->store('announcements', 'public');
        }

        $announcement = Announcement::query()->create([
            'cooperative_id' => $actor->cooperative_id,
            'title' => $data['title'],
            'slug' => $slug,
            'summary' => $data['summary'] ?? null,
            'content' => $data['content'] ?? null,
            'image_path' => $imagePath,
            'audience' => $data['audience'],
            'priority' => $data['priority'] ?? 'normal',
            'send_via' => $data['send_via'] ?? 'in_app',
            'is_pinned' => $data['is_pinned'] ?? false,
            'status' => AnnouncementStatus::Draft->value,
            'published_at' => $data['published_at'] ?? null,
            'expires_at' => $data['expires_at'] ?? null,
            'created_by' => $actor->id,
            'updated_by' => $actor->id,
        ]);

        $this->auditLogs->record('announcement_created', $announcement, [], $announcement->toArray());

        return $announcement;
    }

    public function update(Announcement $announcement, array $data, User $actor): void
    {
        $oldValues = $announcement->toArray();

        $fillable = [
            'title', 'slug', 'summary', 'content', 'audience',
            'priority', 'send_via', 'is_pinned',
            'published_at', 'expires_at',
        ];

        $updateData = [];
        foreach ($fillable as $field) {
            if (array_key_exists($field, $data)) {
                $updateData[$field] = $data[$field];
            }
        }

        if (! empty($data['slug']) && empty($updateData['slug'])) {
            $updateData['slug'] = str($data['title'] ?? $announcement->title)->slug();
        }

        if (! empty($data['image']) && $data['image']->isValid()) {
            if ($announcement->image_path) {
                Storage::disk('public')->delete($announcement->image_path);
            }
            $updateData['image_path'] = $data['image']->store('announcements', 'public');
        }

        $updateData['updated_by'] = $actor->id;

        $announcement->update($updateData);

        $this->auditLogs->record('announcement_updated', $announcement, $oldValues, $announcement->fresh()->toArray());
    }

    public function publish(Announcement $announcement, ?array $targetUserIds, User $actor): void
    {
        $oldValues = $announcement->toArray();

        $announcement->update([
            'status' => AnnouncementStatus::Published->value,
            'published_at' => $announcement->published_at ?? now(),
            'updated_by' => $actor->id,
        ]);

        if (! empty($targetUserIds)) {
            $this->sendToSpecificUsers($announcement, $targetUserIds);
        }

        $this->auditLogs->record('announcement_published', $announcement, $oldValues, $announcement->fresh()->toArray());
    }

    public function unpublish(Announcement $announcement, User $actor): void
    {
        $oldValues = $announcement->toArray();

        $announcement->update([
            'status' => AnnouncementStatus::Draft->value,
            'updated_by' => $actor->id,
        ]);

        $this->auditLogs->record('announcement_unpublished', $announcement, $oldValues, $announcement->fresh()->toArray());
    }

    public function togglePin(Announcement $announcement, User $actor): void
    {
        $newPinState = ! $announcement->is_pinned;

        $announcement->update([
            'is_pinned' => $newPinState,
            'updated_by' => $actor->id,
        ]);

        $action = $newPinState ? 'announcement_pinned' : 'announcement_unpinned';
        $this->auditLogs->record($action, $announcement, [], ['is_pinned' => $newPinState]);
    }

    public function destroy(Announcement $announcement, User $actor): void
    {
        $oldValues = $announcement->toArray();

        $announcement->update(['updated_by' => $actor->id]);
        $announcement->delete();

        $this->auditLogs->record('announcement.deleted', $announcement, $oldValues, []);
    }

    public function markAsRead(Announcement $announcement, User $user): void
    {
        $pivot = DB::table('announcement_user')
            ->where('announcement_id', $announcement->id)
            ->where('user_id', $user->id)
            ->first();

        if ($pivot) {
            DB::table('announcement_user')
                ->where('announcement_id', $announcement->id)
                ->where('user_id', $user->id)
                ->update(['read_at' => now()]);
        } else {
            DB::table('announcement_user')->insert([
                'announcement_id' => $announcement->id,
                'user_id' => $user->id,
                'read_at' => now(),
                'target_type' => 'audience',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function sendToSpecificUsers(Announcement $announcement, array $userIds): void
    {
        $users = User::query()->whereIn('id', $userIds)->get();
        $now = now();

        $pivotData = [];
        foreach ($users as $user) {
            $pivotData[] = [
                'announcement_id' => $announcement->id,
                'user_id' => $user->id,
                'read_at' => null,
                'target_type' => 'specific',
                'notification_id' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            try {
                $user->notify(new AnnouncementPublished($announcement));
                $notificationId = $user->notifications()->latest()->first()?->id ?? null;
                if ($notificationId) {
                    $pivotData[count($pivotData) - 1]['notification_id'] = $notificationId;
                }
            } catch (\Throwable $e) {
                Log::warning('Failed to send announcement notification', [
                    'announcement_id' => $announcement->id,
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        DB::table('announcement_user')->insert($pivotData);
    }
}
