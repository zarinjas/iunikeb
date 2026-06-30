<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\InteractsWithActiveCooperative;
use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BannerController extends Controller
{
    use InteractsWithActiveCooperative;

    public function __construct(
        private readonly AuditLogService $auditLog,
    ) {}

    public function index(Request $request): Response
    {
        $cooperative = $this->activeCooperative();

        $banners = Banner::query()
            ->where('cooperative_id', $cooperative?->id)
            ->ordered()
            ->paginate(15)
            ->withQueryString()
            ->through(fn (Banner $banner) => [
                'id' => $banner->id,
                'image_url' => $banner->imageUrl(),
                'link_url' => $banner->link_url,
                'is_active' => $banner->is_active,
                'sort_order' => $banner->sort_order,
                'created_at' => $banner->created_at?->format('d/m/Y H:i'),
            ]);

        return Inertia::render('Admin/Pages/Banners/Index', [
            'banners' => $banners,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Pages/Banners/Form', [
            'banner' => null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $cooperative = $this->activeCooperative();

        $validated = $request->validate([
            'image' => ['required', 'file', 'mimes:jpg,jpeg,png', 'max:1024'],
            'link_url' => ['nullable', 'url', 'max:255'],
        ], [
            'image.required' => 'Sila pilih gambar banner.',
            'image.mimes' => 'Format gambar mestilah JPG atau PNG sahaja.',
            'image.max' => 'Saiz fail tidak boleh melebihi 1MB.',
            'link_url.url' => 'Pautan mestilah URL yang sah.',
        ]);

        $imagePath = $request->file('image')->store('banners', 'public');

        $banner = Banner::query()->create([
            'cooperative_id' => $cooperative?->id,
            'image_path' => $imagePath,
            'link_url' => $validated['link_url'] ?? null,
            'sort_order' => 0,
            'is_active' => true,
            'created_by' => $request->user()?->id,
            'updated_by' => $request->user()?->id,
        ]);

        $this->auditLog->record('banner.created', $banner, newValues: $banner->toArray());

        return redirect()->route('admin.banners.index')->with('status', 'Banner berjaya ditambah.');
    }

    public function edit(Banner $banner): Response
    {
        $this->ensureSameCooperative($banner);

        return Inertia::render('Admin/Pages/Banners/Form', [
            'banner' => [
                'id' => $banner->id,
                'image_url' => $banner->imageUrl(),
                'image_path' => $banner->image_path,
                'link_url' => $banner->link_url,
                'is_active' => $banner->is_active,
            ],
        ]);
    }

    public function update(Request $request, Banner $banner): RedirectResponse
    {
        $this->ensureSameCooperative($banner);

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
            if ($banner->image_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($banner->image_path);
            }
            $data['image_path'] = $request->file('image')->store('banners', 'public');
        }

        $old = $banner->toArray();
        $banner->update($data);

        $this->auditLog->record('banner.updated', $banner, oldValues: $old, newValues: $banner->fresh()->toArray());

        return redirect()->route('admin.banners.index')->with('status', 'Banner berjaya dikemas kini.');
    }

    public function destroy(Banner $banner): RedirectResponse
    {
        $this->ensureSameCooperative($banner);

        if ($banner->image_path) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($banner->image_path);
        }

        $this->auditLog->record('banner.deleted', $banner, oldValues: $banner->toArray());
        $banner->delete();

        return back()->with('status', 'Banner berjaya dipadam.');
    }

    public function toggle(Banner $banner): RedirectResponse
    {
        $this->ensureSameCooperative($banner);

        $old = $banner->toArray();
        $banner->update(['is_active' => ! $banner->is_active]);

        $this->auditLog->record('banner.toggled', $banner, oldValues: $old, newValues: $banner->fresh()->toArray());

        return back()->with('status', $banner->is_active ? 'Banner diaktifkan.' : 'Banner dinyahaktifkan.');
    }

    public function reorder(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ordered_ids' => ['required', 'array'],
            'ordered_ids.*' => ['integer'],
        ]);

        $cooperative = $this->activeCooperative();

        foreach ($validated['ordered_ids'] as $index => $id) {
            Banner::query()
                ->where('cooperative_id', $cooperative?->id)
                ->where('id', $id)
                ->update(['sort_order' => $index]);
        }

        return back()->with('status', 'Susunan banner dikemas kini.');
    }
}
