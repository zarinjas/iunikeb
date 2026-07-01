<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Cooperative;
use App\Services\Settings\SettingsService;
use Inertia\Inertia;
use Inertia\Response;

class AnnouncementController extends Controller
{
    public function __construct(
        private readonly SettingsService $settings,
    ) {}

    public function index(): Response
    {
        $announcements = Announcement::query()
            ->forCooperative($this->activeCooperative()?->id)
            ->published()
            ->notExpired()
            ->forAudience('public')
            ->latest('published_at')
            ->paginate(12)
            ->withQueryString()
            ->through(fn (Announcement $announcement) => [
                'id' => $announcement->id,
                'title' => $announcement->title,
                'slug' => $announcement->slug,
                'summary' => $announcement->summary,
                'priority' => $announcement->priority,
                'is_pinned' => $announcement->is_pinned,
                'published_at' => $announcement->published_at?->format('d/m/Y'),
                'show_url' => route('public.announcements.show', $announcement->slug),
            ]);

        return Inertia::render('Public/Pages/Announcements/Index', [
            'announcements' => $announcements,
        ]);
    }

    public function show(string $slug): Response
    {
        $announcement = Announcement::query()
            ->forCooperative($this->activeCooperative()?->id)
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        return Inertia::render('Public/Pages/Announcements/Show', [
            'announcement' => [
                'id' => $announcement->id,
                'title' => $announcement->title,
                'slug' => $announcement->slug,
                'summary' => $announcement->summary,
                'content' => $announcement->content,
                'priority' => $announcement->priority,
                'is_pinned' => $announcement->is_pinned,
                'image_url' => $announcement->image_path
                    ? \Illuminate\Support\Facades\Storage::disk('public')->url($announcement->image_path)
                    : null,
                'published_at' => $announcement->published_at?->format('d/m/Y'),
                'expires_at' => $announcement->expires_at?->format('d/m/Y'),
            ],
        ]);
    }

    private function activeCooperative(): ?Cooperative
    {
        return $this->settings->activeCooperative();
    }
}
