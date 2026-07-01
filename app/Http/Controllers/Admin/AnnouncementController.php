<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AnnouncementAudience;
use App\Enums\AnnouncementPriority;
use App\Enums\AnnouncementStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAnnouncementRequest;
use App\Http\Requests\Admin\UpdateAnnouncementRequest;
use App\Models\Announcement;
use App\Models\Cooperative;
use App\Models\User;
use App\Services\AnnouncementService;
use App\Services\Settings\SettingsService;
use App\Support\AccessControl;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AnnouncementController extends Controller
{
    public function __construct(
        private readonly SettingsService $settings,
        private readonly AnnouncementService $announcements,
    ) {}

    public function index(Request $request): Response
    {
        $search = trim((string) $request->string('search'));
        $status = $request->string('status')->toString();
        $audience = $request->string('audience')->toString();
        $priority = $request->string('priority')->toString();

        $announcements = Announcement::query()
            ->forCooperative($this->activeCooperative()?->id)
            ->with(['createdBy', 'updatedBy'])
            ->when($search !== '', fn ($query) => $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('summary', 'like', "%{$search}%");
            }))
            ->when(in_array($status, AnnouncementStatus::values(), true), fn ($query) => $query->where('status', $status))
            ->when(in_array($audience, AnnouncementAudience::values(), true), fn ($query) => $query->where('audience', $audience))
            ->when(in_array($priority, AnnouncementPriority::values(), true), fn ($query) => $query->where('priority', $priority))
            ->latest('updated_at')
            ->paginate(10)
            ->withQueryString()
            ->through(fn (Announcement $a) => $this->serializeSummary($a));

        return Inertia::render('Admin/Pages/Announcements/Index', [
            'filters' => [
                'search' => $search,
                'status' => $status,
                'audience' => $audience,
                'priority' => $priority,
            ],
            'announcements' => $announcements,
            'statusOptions' => $this->statusOptions(includeAll: true),
            'audienceOptions' => $this->audienceOptions(includeAll: true),
            'priorityOptions' => $this->priorityOptions(includeAll: true),
            'canCreate' => $request->user()?->can(AccessControl::PERMISSION_CREATE_ANNOUNCEMENTS) ?? false,
            'canPublish' => $request->user()?->can(AccessControl::PERMISSION_PUBLISH_ANNOUNCEMENTS) ?? false,
            'canDelete' => $request->user()?->can(AccessControl::PERMISSION_DELETE_ANNOUNCEMENTS) ?? false,
        ]);
    }

    public function create(Request $request): Response
    {
        return Inertia::render('Admin/Pages/Announcements/Create', [
            'audienceOptions' => $this->audienceOptions(),
            'priorityOptions' => $this->priorityOptions(),
            'sendViaOptions' => [
                ['value' => 'in_app', 'label' => 'In-App sahaja'],
                ['value' => 'in_app,email', 'label' => 'In-App + E-mel'],
            ],
            'memberOptions' => $this->memberOptions(),
        ]);
    }

    public function store(StoreAnnouncementRequest $request): RedirectResponse
    {
        $announcement = $this->announcements->store($request->validated(), $request->user());

        return redirect()
            ->route('admin.announcements.edit', $announcement)
            ->with('status', 'Pengumuman berjaya disimpan sebagai draf.');
    }

    public function show(Request $request, Announcement $announcement): Response
    {
        $this->ensureSameCooperative($announcement);
        $announcement->loadMissing(['createdBy', 'targetedUsers']);

        return Inertia::render('Admin/Pages/Announcements/Show', [
            'announcement' => $this->serializeDetail($announcement),
            'canPublish' => $request->user()?->can(AccessControl::PERMISSION_PUBLISH_ANNOUNCEMENTS) ?? false,
            'canEdit' => $request->user()?->can(AccessControl::PERMISSION_EDIT_ANNOUNCEMENTS) ?? false,
            'canDelete' => $request->user()?->can(AccessControl::PERMISSION_DELETE_ANNOUNCEMENTS) ?? false,
        ]);
    }

    public function edit(Request $request, Announcement $announcement): Response
    {
        $this->ensureSameCooperative($announcement);
        $announcement->loadMissing('targetedUsers');

        return Inertia::render('Admin/Pages/Announcements/Edit', [
            'announcement' => $this->serializeDetail($announcement),
            'audienceOptions' => $this->audienceOptions(),
            'priorityOptions' => $this->priorityOptions(),
            'sendViaOptions' => [
                ['value' => 'in_app', 'label' => 'In-App sahaja'],
                ['value' => 'in_app,email', 'label' => 'In-App + E-mel'],
            ],
            'memberOptions' => $this->memberOptions(),
            'canPublish' => $request->user()?->can(AccessControl::PERMISSION_PUBLISH_ANNOUNCEMENTS) ?? false,
        ]);
    }

    public function update(UpdateAnnouncementRequest $request, Announcement $announcement): RedirectResponse
    {
        $this->ensureSameCooperative($announcement);

        $this->announcements->update($announcement, $request->validated(), $request->user());

        return back()->with('status', 'Pengumuman berjaya dikemas kini.');
    }

    public function destroy(Request $request, Announcement $announcement): RedirectResponse
    {
        $this->ensureSameCooperative($announcement);

        $this->announcements->destroy($announcement, $request->user());

        return redirect()
            ->route('admin.announcements.index')
            ->with('status', 'Pengumuman berjaya dipadam.');
    }

    public function publish(Request $request, Announcement $announcement): RedirectResponse
    {
        $this->ensureSameCooperative($announcement);

        $targetUserIds = $request->input('target_user_ids', []);
        $this->announcements->publish($announcement, $targetUserIds ?: null, $request->user());

        return back()->with('status', 'Pengumuman berjaya diterbitkan.');
    }

    public function unpublish(Request $request, Announcement $announcement): RedirectResponse
    {
        $this->ensureSameCooperative($announcement);

        $this->announcements->unpublish($announcement, $request->user());

        return back()->with('status', 'Pengumuman tidak lagi diterbitkan.');
    }

    public function togglePin(Request $request, Announcement $announcement): RedirectResponse
    {
        $this->ensureSameCooperative($announcement);

        $this->announcements->togglePin($announcement, $request->user());

        return back()->with('status', 'Status pin pengumuman berjaya dikemas kini.');
    }

    public function readStats(Request $request, Announcement $announcement): Response
    {
        $this->ensureSameCooperative($announcement);

        $users = $announcement->targetedUsers()
            ->orderBy('name')
            ->paginate(20)
            ->through(fn (User $user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'read_at' => $user->pivot?->read_at
                    ? \Illuminate\Support\Carbon::parse($user->pivot->read_at)->format('d/m/Y H:i')
                    : null,
                'is_read' => ! is_null($user->pivot?->read_at),
                'target_type' => $user->pivot?->target_type ?? 'audience',
            ]);

        return Inertia::render('Admin/Pages/Announcements/ReadStats', [
            'announcement' => $this->serializeSummary($announcement),
            'users' => $users,
        ]);
    }

    private function serializeSummary(Announcement $announcement): array
    {
        return [
            'id' => $announcement->id,
            'title' => $announcement->title,
            'slug' => $announcement->slug,
            'summary' => $announcement->summary,
            'audience' => $announcement->audience,
            'audience_label' => $this->audienceLabel($announcement->audience),
            'status' => $announcement->status,
            'priority' => $announcement->priority,
            'priority_label' => $this->priorityLabel($announcement->priority),
            'is_pinned' => $announcement->is_pinned,
            'created_by_name' => $announcement->createdBy?->name,
            'published_at' => $announcement->published_at?->format('d/m/Y H:i'),
            'expires_at' => $announcement->expires_at?->format('d/m/Y H:i'),
            'show_url' => route('admin.announcements.show', $announcement),
            'edit_url' => route('admin.announcements.edit', $announcement),
            'targeted_count' => $announcement->targetedUsers()->count(),
            'read_count' => $announcement->targetedUsers()->whereNotNull('read_at')->count(),
        ];
    }

    private function serializeDetail(Announcement $announcement): array
    {
        return array_merge($this->serializeSummary($announcement), [
            'content' => $announcement->content,
            'image_path' => $announcement->image_path,
            'image_url' => $announcement->image_path
                ? \Illuminate\Support\Facades\Storage::disk('public')->url($announcement->image_path)
                : null,
            'send_via' => $announcement->send_via,
            'created_at' => $announcement->created_at?->format('d/m/Y H:i'),
            'updated_at' => $announcement->updated_at?->format('d/m/Y H:i'),
            'targeted_user_ids' => $announcement->targetedUsers()->pluck('user_id')->toArray(),
        ]);
    }

    private function activeCooperative(): ?Cooperative
    {
        return $this->settings->activeCooperative();
    }

    private function ensureSameCooperative(Announcement $announcement): void
    {
        abort_unless($announcement->cooperative_id === $this->activeCooperative()?->id, 404);
    }

    private function statusOptions(bool $includeAll = false): array
    {
        $options = [
            ['value' => AnnouncementStatus::Draft->value, 'label' => 'Draf'],
            ['value' => AnnouncementStatus::Published->value, 'label' => 'Diterbitkan'],
            ['value' => AnnouncementStatus::Archived->value, 'label' => 'Diarkibkan'],
        ];

        return $includeAll ? [['value' => '', 'label' => 'Semua status'], ...$options] : $options;
    }

    private function audienceOptions(bool $includeAll = false): array
    {
        $options = [
            ['value' => AnnouncementAudience::Public->value, 'label' => 'Public'],
            ['value' => AnnouncementAudience::Members->value, 'label' => 'Ahli'],
            ['value' => AnnouncementAudience::Admins->value, 'label' => 'Admin'],
        ];

        return $includeAll ? [['value' => '', 'label' => 'Semua audiens'], ...$options] : $options;
    }

    private function priorityOptions(bool $includeAll = false): array
    {
        $options = [
            ['value' => AnnouncementPriority::Normal->value, 'label' => 'Normal'],
            ['value' => AnnouncementPriority::Penting->value, 'label' => 'Penting'],
            ['value' => AnnouncementPriority::Segera->value, 'label' => 'Segera'],
        ];

        return $includeAll ? [['value' => '', 'label' => 'Semua keutamaan'], ...$options] : $options;
    }

    private function memberOptions(): array
    {
        return User::query()
            ->where('cooperative_id', $this->activeCooperative()?->id)
            ->where('user_type', AccessControl::ROLE_MEMBER)
            ->orderBy('name')
            ->get()
            ->map(fn (User $user) => [
                'value' => $user->id,
                'label' => $user->name . ' (' . ($user->email ?? $user->phone ?? '-') . ')',
            ])
            ->values()
            ->all();
    }

    private function audienceLabel(string $value): string
    {
        return collect($this->audienceOptions())
            ->firstWhere('value', $value)['label'] ?? $value;
    }

    private function priorityLabel(string $value): string
    {
        return collect($this->priorityOptions())
            ->firstWhere('value', $value)['label'] ?? $value;
    }
}
