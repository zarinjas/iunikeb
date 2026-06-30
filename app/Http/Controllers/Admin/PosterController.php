<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\InteractsWithActiveCooperative;
use App\Http\Controllers\Controller;
use App\Models\Poster;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class PosterController extends Controller
{
    use InteractsWithActiveCooperative;

    public function __construct(
        private readonly AuditLogService $auditLog,
    ) {}

    public function index(Request $request): Response
    {
        $cooperative = $this->activeCooperative();
        $search = trim((string) $request->string('search'));
        $type = trim((string) $request->string('type'));

        $posters = Poster::query()
            ->where('cooperative_id', $cooperative?->id)
            ->when($search !== '', fn ($q) => $q->where('title', 'like', "%{$search}%"))
            ->when($type !== '', fn ($q) => $q->where('type', $type))
            ->latest()
            ->paginate(15)
            ->withQueryString()
            ->through(fn (Poster $poster) => [
                'id' => $poster->id,
                'title' => $poster->title,
                'type' => $poster->type,
                'audience' => $poster->audience,
                'is_active' => $poster->is_active,
                'sort_order' => $poster->sort_order,
                'image_url' => $poster->imageUrl(),
                'link_url' => $poster->link_url,
                'updated_at' => $poster->updated_at?->format('d/m/Y H:i'),
            ]);

        return Inertia::render('Admin/Pages/Posters/Index', [
            'filters' => [
                'search' => $search,
                'type' => $type,
            ],
            'posters' => $posters,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Pages/Posters/Form', [
            'poster' => null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $cooperative = $this->activeCooperative();
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'alt_text' => ['nullable', 'string', 'max:255'],
            'image_path' => ['required', 'string', 'max:255'],
            'link_url' => ['nullable', 'string', 'max:255'],
            'type' => ['required', Rule::in(['banner', 'poster'])],
            'audience' => ['required', Rule::in(['public', 'members', 'both'])],
            'is_active' => ['required', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $poster = Poster::query()->create([
            'cooperative_id' => $cooperative?->id,
            'title' => $validated['title'],
            'alt_text' => $validated['alt_text'] ?? null,
            'image_path' => $validated['image_path'],
            'link_url' => $validated['link_url'] ?? null,
            'type' => $validated['type'],
            'audience' => $validated['audience'],
            'is_active' => (bool) $validated['is_active'],
            'sort_order' => $validated['sort_order'] ?? 0,
            'created_by' => $request->user()?->id,
            'updated_by' => $request->user()?->id,
        ]);

        $this->auditLog->record('poster.created', $poster, newValues: $poster->toArray());

        return redirect()->route('admin.posters.index')->with('status', 'Poster berjaya ditambah.');
    }

    public function edit(Poster $poster): Response
    {
        $this->ensureSameCooperative($poster);

        return Inertia::render('Admin/Pages/Posters/Form', [
            'poster' => [
                'id' => $poster->id,
                'title' => $poster->title,
                'alt_text' => $poster->alt_text,
                'image_path' => $poster->image_path,
                'image_url' => $poster->imageUrl(),
                'link_url' => $poster->link_url,
                'type' => $poster->type,
                'audience' => $poster->audience,
                'is_active' => $poster->is_active,
                'sort_order' => $poster->sort_order,
            ],
        ]);
    }

    public function update(Request $request, Poster $poster): RedirectResponse
    {
        $this->ensureSameCooperative($poster);
        $cooperative = $this->activeCooperative();

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'alt_text' => ['nullable', 'string', 'max:255'],
            'image_path' => ['required', 'string', 'max:255'],
            'link_url' => ['nullable', 'string', 'max:255'],
            'type' => ['required', Rule::in(['banner', 'poster'])],
            'audience' => ['required', Rule::in(['public', 'members', 'both'])],
            'is_active' => ['required', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $old = $poster->toArray();

        $poster->update([
            'title' => $validated['title'],
            'alt_text' => $validated['alt_text'] ?? null,
            'image_path' => $validated['image_path'],
            'link_url' => $validated['link_url'] ?? null,
            'type' => $validated['type'],
            'audience' => $validated['audience'],
            'is_active' => (bool) $validated['is_active'],
            'sort_order' => $validated['sort_order'] ?? 0,
            'updated_by' => $request->user()?->id,
        ]);

        $this->auditLog->record('poster.updated', $poster, oldValues: $old, newValues: $poster->fresh()->toArray());

        return redirect()->route('admin.posters.index')->with('status', 'Poster berjaya dikemas kini.');
    }

    public function destroy(Poster $poster): RedirectResponse
    {
        $this->ensureSameCooperative($poster);

        $this->auditLog->record('poster.deleted', $poster, oldValues: $poster->toArray());
        $poster->delete();

        return back()->with('status', 'Poster berjaya dipadam.');
    }
}
