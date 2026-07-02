<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\FrontpageSection;
use App\Services\Settings\SettingsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PopupController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user || $user->role !== 'member') {
            return response()->json(null);
        }

        if ($request->session()->get('popup_dismissed')) {
            return response()->json(null);
        }

        $cooperative = app(SettingsService::class)->activeCooperative();
        if (! $cooperative) {
            return response()->json(null);
        }

        $section = FrontpageSection::query()
            ->where('cooperative_id', $cooperative->id)
            ->where('key', 'member_popup')
            ->where('is_active', true)
            ->first();

        if (! $section) {
            return response()->json(null);
        }

        $section->load('items');

        if ($section->items->isEmpty()) {
            return response()->json(null);
        }

        $item = $section->items->first();

        return response()->json([
            'image_url' => $item->imageUrl(),
            'title' => $item->title,
            'content' => $item->description,
            'button_text' => $item->button_text,
            'button_url' => $item->button_url,
        ]);
    }
}
