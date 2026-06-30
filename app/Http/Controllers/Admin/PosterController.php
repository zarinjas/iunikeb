<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\InteractsWithActiveCooperative;
use App\Http\Controllers\Controller;
use App\Models\Poster;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

        $posters = Poster::query()
            ->where('cooperative_id', $cooperative?->id)
            ->ordered()
            ->paginate(15)
            ->withQueryString()
            ->through(fn (Poster $poster) => [
                'id' => $poster->id,
                'image_url' => $poster->imageUrl(),
                'link_url' => $poster->link_url,
                'is_active' => $poster->is_active,
                'sort_order' => $poster->sort_order,
                'created_at' => $poster->created_at?->format('d/m/Y H:i'),
            ]);

        return Inertia::render('Admin/Pages/Posters/Index', [
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
            'image' => ['required', 'file', 'mimes:jpg,jpeg,png', 'max:1024'],
            'link_url' => ['nullable', 'url', 'max:255'],
        ], [
            'image.required' => 'Sila pilih gambar poster.',
            'image.mimes' => 'Format gambar mestilah JPG atau PNG sahaja.',
            'image.max' => 'Saiz fail tidak boleh melebihi 1MB.',
            'link_url.url' => 'Pautan mestilah URL yang sah.',
        ]);

        $imagePath = $request->file('image')->store('posters', 'public');

        $poster = Poster::query()->create([
            'cooperative_id' => $cooperative?->id,
            'image_path' => $imagePath,
            'link_url' => $validated['link_url'] ?? null,
            'sort_order' => 0,
            'is_active' => true,
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
                'image_url' => $poster->imageUrl(),
                'image_path' => $poster->image_path,
                'link_url' => $poster->link_url,
                'is_active' => $poster->is_active,
            ],
        ]);
    }

    public function update(Request $request, Poster $poster): RedirectResponse
    {
        $this->ensureSameCooperative($poster);

        $validated = $request->validate([
            'image' => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:1024'],
            'link_url' => ['nullable', 'url', 'max:255'],
        ], [
            'image.mimes' => 'Format gambar mestilah JPG atau PNG sahaja.',
            'image.max' => 'Saiz fail tidak boleh melebihi 1MB.',
            'link_url.url' => 'Pautan mestilah URL yang sah.',
        ]);

        $data = [
            'link_url' => $validated['link_url'] ?? null,
            'updated_by' => $request->user()?->id,
        ];

        if ($request->hasFile('image')) {
            if ($poster->image_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($poster->image_path);
            }
            $data['image_path'] = $request->file('image')->store('posters', 'public');
        }

        $old = $poster->toArray();
        $poster->update($data);

        $this->auditLog->record('poster.updated', $poster, oldValues: $old, newValues: $poster->fresh()->toArray());

        return redirect()->route('admin.posters.index')->with('status', 'Poster berjaya dikemas kini.');
    }

    public function destroy(Poster $poster): RedirectResponse
    {
        $this->ensureSameCooperative($poster);

        if ($poster->image_path) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($poster->image_path);
        }

        $this->auditLog->record('poster.deleted', $poster, oldValues: $poster->toArray());
        $poster->delete();

        return back()->with('status', 'Poster berjaya dipadam.');
    }

    public function toggle(Poster $poster): RedirectResponse
    {
        $this->ensureSameCooperative($poster);

        $old = $poster->toArray();
        $poster->update(['is_active' => ! $poster->is_active]);

        $this->auditLog->record('poster.toggled', $poster, oldValues: $old, newValues: $poster->fresh()->toArray());

        return back()->with('status', $poster->is_active ? 'Poster diaktifkan.' : 'Poster dinyahaktifkan.');
    }

    public function reorder(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ordered_ids' => ['required', 'array'],
            'ordered_ids.*' => ['integer'],
        ]);

        $cooperative = $this->activeCooperative();

        foreach ($validated['ordered_ids'] as $index => $id) {
            Poster::query()
                ->where('cooperative_id', $cooperative?->id)
                ->where('id', $id)
                ->update(['sort_order' => $index]);
        }

        return back()->with('status', 'Susunan poster dikemas kini.');
    }
}
