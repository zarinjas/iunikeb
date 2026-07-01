<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\InteractsWithActiveCooperative;
use App\Models\FrontpageSection;
use App\Models\Menu;
use Inertia\Inertia;
use Inertia\Response;

class FrontpageController extends Controller
{
    use InteractsWithActiveCooperative;

    public function index(): Response
    {
        $cooperative = $this->activeCooperative();
        $cooperativeId = $cooperative?->id;

        $sections = FrontpageSection::query()
            ->where('cooperative_id', $cooperativeId)
            ->active()
            ->where('key', '!=', 'member_popup')
            ->with('items')
            ->get()
            ->keyBy('key')
            ->map(fn (FrontpageSection $section) => [
                'id' => $section->id,
                'key' => $section->key,
                'title' => $section->title,
                'subtitle' => $section->subtitle,
                'data' => $section->data,
                'items' => $section->items->map(fn ($item) => [
                    'id' => $item->id,
                    'title' => $item->title,
                    'subtitle' => $item->subtitle,
                    'description' => $item->description,
                    'image' => $item->imageUrl(),
                    'icon' => $item->icon,
                    'value' => $item->value,
                    'button_text' => $item->button_text,
                    'button_url' => $item->button_url,
                    'extra' => $item->extra,
                ])->values(),
            ]);

        // Navigation menus
        $headerMenus = Menu::query()
            ->where('cooperative_id', $cooperativeId)
            ->location('header')
            ->active()
            ->root()
            ->with('children')
            ->orderBy('sort_order')
            ->get()
            ->map(fn (Menu $menu) => [
                'label' => $menu->label,
                'url' => $menu->url,
                'children' => $menu->children->map(fn ($child) => [
                    'label' => $child->label,
                    'url' => $child->url,
                ])->values(),
            ]);

        $footerMenus = Menu::query()
            ->where('cooperative_id', $cooperativeId)
            ->where('location', 'like', 'footer%')
            ->active()
            ->orderBy('location')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('location')
            ->map(fn ($items) => $items->map(fn ($item) => [
                'label' => $item->label,
                'url' => $item->url,
            ])->values());

        return Inertia::render('Public/Pages/Frontpage', [
            'sections' => $sections,
            'menus' => [
                'header' => $headerMenus,
                'footer' => $footerMenus,
            ],
        ]);
    }
}
