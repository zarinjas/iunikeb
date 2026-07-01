<?php

namespace App\Http\Controllers\Member;

use App\Enums\AnnouncementAudience;
use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Cooperative;
use App\Services\AnnouncementService;
use App\Services\Settings\SettingsService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AnnouncementController extends Controller
{
    public function __construct(
        private readonly SettingsService $settings,
        private readonly AnnouncementService $announcements,
    ) {}

    public function index(): Response
    {
        $member = auth()->user();

        $announcements = Announcement::query()
            ->forCooperative($this->activeCooperative()?->id)
            ->published()
            ->notExpired()
            ->forMember($member)
            ->latest('published_at')
            ->paginate(12)
            ->withQueryString()
            ->through(fn (Announcement $announcement) => $this->serializeForMember($announcement, $member));

        return Inertia::render('Member/Pages/Announcements/Index', [
            'announcements' => $announcements,
        ]);
    }

    public function show(Announcement $announcement): Response
    {
        $this->ensureSameCooperative($announcement);

        $member = auth()->user();
        $this->announcements->markAsRead($announcement, $member);

        return Inertia::render('Member/Pages/Announcements/Show', [
            'announcement' => $this->serializeDetail($announcement),
        ]);
    }

    private function serializeForMember(Announcement $announcement, $member): array
    {
        $isRead = $announcement->targetedUsers()
            ->where('user_id', $member->id)
            ->whereNotNull('read_at')
            ->exists();

        return [
            'id' => $announcement->id,
            'title' => $announcement->title,
            'slug' => $announcement->slug,
            'summary' => $announcement->summary,
            'audience' => $announcement->audience,
            'audience_label' => $announcement->audience === AnnouncementAudience::Members->value ? 'Ahli' : 'Public',
            'priority' => $announcement->priority,
            'is_pinned' => $announcement->is_pinned,
            'is_read' => $isRead,
            'published_at' => $announcement->published_at?->format('d/m/Y'),
            'show_url' => route('member.announcements.show', ['announcement' => $announcement->slug]),
        ];
    }

    private function serializeDetail(Announcement $announcement): array
    {
        return [
            'id' => $announcement->id,
            'title' => $announcement->title,
            'slug' => $announcement->slug,
            'summary' => $announcement->summary,
            'content' => $announcement->content,
            'audience' => $announcement->audience,
            'audience_label' => $announcement->audience === AnnouncementAudience::Members->value ? 'Ahli' : 'Public',
            'priority' => $announcement->priority,
            'is_pinned' => $announcement->is_pinned,
            'image_url' => $announcement->image_path
                ? \Illuminate\Support\Facades\Storage::disk('public')->url($announcement->image_path)
                : null,
            'published_at' => $announcement->published_at?->format('d/m/Y'),
            'expires_at' => $announcement->expires_at?->format('d/m/Y'),
        ];
    }

    private function activeCooperative(): ?Cooperative
    {
        return $this->settings->activeCooperative();
    }

    private function ensureSameCooperative(Announcement $announcement): void
    {
        abort_unless($announcement->cooperative_id === $this->activeCooperative()?->id, 404);
    }
}
