<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\InteractsWithActiveCooperative;
use App\Models\FrontpageSection;
use App\Models\FrontpageSectionItem;
use App\Models\Menu;
use App\Services\Files\MediaFileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class FrontpageController extends Controller
{
    use InteractsWithActiveCooperative;

    private const SECTION_KEYS = [
        'hero', 'stats', 'services', 'benefit', 'business',
        'promotion', 'membership', 'footer', 'member_popup',
    ];

    public function index(Request $request): Response
    {
        $cooperative = $this->activeCooperative();
        $cooperativeId = $cooperative?->id;

        $sections = FrontpageSection::query()
            ->where('cooperative_id', $cooperativeId)
            ->withCount('items')
            ->orderByRaw("case key
                when 'hero' then 1
                when 'stats' then 2
                when 'services' then 3
                when 'benefit' then 4
                when 'business' then 5
                when 'promotion' then 6
                when 'membership' then 7
                when 'footer' then 8
                when 'member_popup' then 9
                else 99 end")
            ->get();

        // Ensure all section keys exist
        $existing = $sections->pluck('key')->all();
        foreach (self::SECTION_KEYS as $key) {
            if (! in_array($key, $existing, true)) {
                FrontpageSection::create([
                    'cooperative_id' => $cooperativeId,
                    'key' => $key,
                    'title' => $this->defaultTitle($key),
                ]);
            }
        }

        // Reload
        $sections = FrontpageSection::query()
            ->where('cooperative_id', $cooperativeId)
            ->withCount('items')
            ->orderByRaw("case key
                when 'hero' then 1
                when 'stats' then 2
                when 'services' then 3
                when 'benefit' then 4
                when 'business' then 5
                when 'promotion' then 6
                when 'membership' then 7
                when 'footer' then 8
                when 'member_popup' then 9
                else 99 end")
            ->get()
            ->map(fn ($s) => [
                'id' => $s->id,
                'key' => $s->key,
                'title' => $s->title,
                'is_active' => $s->is_active,
                'items_count' => $s->items_count,
            ]);

        return Inertia::render('Admin/Pages/Frontpage/Index', [
            'sections' => $sections,
        ]);
    }

    public function edit(Request $request, FrontpageSection $section): Response
    {
        $section->load('allItems');

        return Inertia::render('Admin/Pages/Frontpage/SectionEditor', [
            'section' => [
                'id' => $section->id,
                'key' => $section->key,
                'title' => $section->title,
                'subtitle' => $section->subtitle,
                'data' => $section->data,
                'is_active' => $section->is_active,
                'items' => $section->allItems->map(fn ($item) => [
                    'id' => $item->id,
                    'title' => $item->title,
                    'subtitle' => $item->subtitle,
                    'description' => $item->description,
                    'image' => $item->image,
                    'icon' => $item->icon,
                    'value' => $item->value,
                    'button_text' => $item->button_text,
                    'button_url' => $item->button_url,
                    'extra' => $item->extra,
                    'sort_order' => $item->sort_order,
                    'is_active' => $item->is_active,
                ])->values(),
            ],
        ]);
    }

    public function update(Request $request, FrontpageSection $section): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'data' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        \Log::debug('[frontpage-update]', [
            'key' => $section->key,
            'is_active_before' => $section->is_active,
            'is_active_request' => $request->input('is_active'),
        ]);

        $section->update($validated);

        \Log::debug('[frontpage-update] after', [
            'is_active_after' => $section->fresh()->is_active,
        ]);

        return redirect()->back()->with('status', 'Seksyen dikemas kini.');
    }

    public function storeItem(Request $request, FrontpageSection $section): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:2000',
            'image' => 'nullable',
            'icon' => 'nullable|string|max:100',
            'value' => 'nullable|string|max:100',
            'button_text' => 'nullable|string|max:100',
            'button_url' => 'nullable|string|max:500',
            'extra' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $validated['image'] = $this->handleImageUpload($request, null);

        $maxSort = $section->allItems()->max('sort_order') ?? 0;
        $validated['sort_order'] = $maxSort + 1;
        $validated['is_active'] ??= true;

        $section->allItems()->create($validated);

        return redirect()->back()->with('status', 'Item ditambah.');
    }

    public function updateItem(Request $request, FrontpageSection $section, FrontpageSectionItem $item): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:2000',
            'image' => 'nullable',
            'icon' => 'nullable|string|max:100',
            'value' => 'nullable|string|max:100',
            'button_text' => 'nullable|string|max:100',
            'button_url' => 'nullable|string|max:500',
            'extra' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $validated['image'] = $this->handleImageUpload($request, $item->image);

        $item->update($validated);

        return redirect()->back()->with('status', 'Item dikemas kini.');
    }

    public function uploadImage(Request $request): JsonResponse
    {
        if (! $request->hasFile('file')) {
            return response()->json(['error' => 'No file'], 422);
        }

        $path = $request->file('file')->store('frontpage', 'public');

        return response()->json([
            'path' => $path,
            'url' => Storage::disk('public')->url($path),
        ]);
    }

    private function handleImageUpload(Request $request, ?string $existing = null): ?string
    {
        if ($request->hasFile('image')) {
            return $request->file('image')->store('frontpage', 'public');
        }

        $imageValue = $request->input('image');

        if ($imageValue === '' || $imageValue === null) {
            return null;
        }

        return $imageValue !== null ? $imageValue : $existing;
    }

    public function destroyItem(FrontpageSection $section, FrontpageSectionItem $item): RedirectResponse
    {
        $item->delete();

        return redirect()->back()->with('status', 'Item dipadam.');
    }

    public function reorderItems(Request $request, FrontpageSection $section): RedirectResponse
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|integer|exists:frontpage_section_items,id',
            'items.*.sort_order' => 'required|integer|min:0',
        ]);

        foreach ($validated['items'] as $itemData) {
            FrontpageSectionItem::where('id', $itemData['id'])
                ->where('section_id', $section->id)
                ->update(['sort_order' => $itemData['sort_order']]);
        }

        return redirect()->back()->with('status', 'Susunan dikemas kini.');
    }

    private function defaultTitle(string $key): string
    {
        return match ($key) {
            'hero' => 'Hero Slider',
            'stats' => 'Statistik',
            'services' => 'Perkhidmatan',
            'benefit' => 'Manfaat Ahli',
            'business' => 'Perniagaan',
            'promotion' => 'Promosi',
            'membership' => 'Keahlian',
            'footer' => 'Footer',
            'member_popup' => 'Popup Ahli',
            default => $key,
        };
    }
}
