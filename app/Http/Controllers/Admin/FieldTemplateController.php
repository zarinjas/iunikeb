<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\InteractsWithActiveCooperative;
use App\Http\Controllers\Controller;
use App\Models\FieldTemplate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FieldTemplateController extends Controller
{
    use InteractsWithActiveCooperative;

    public function index(Request $request): JsonResponse
    {
        $module = $request->string('module');

        $templates = FieldTemplate::query()
            ->where('cooperative_id', $this->activeCooperative()?->id)
            ->when($module->isNotEmpty(), fn ($q) => $q->where('module', (string) $module))
            ->orderBy('name')
            ->get()
            ->map(fn (FieldTemplate $t) => [
                'id' => $t->id,
                'name' => $t->name,
                'description' => $t->description,
                'module' => $t->module,
                'fieldCount' => count($t->fields_json ?? []),
                'fields' => $t->fields_json ?? [],
            ]);

        return response()->json([
            'ok' => true,
            'templates' => $templates,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'module' => ['nullable', 'string'],
            'fields' => ['required', 'array', 'min:1'],
            'fields.*.type' => ['required', 'string'],
            'fields.*.label' => ['required', 'string', 'max:255'],
            'fields.*.is_required' => ['nullable', 'boolean'],
            'fields.*.placeholder' => ['nullable', 'string'],
            'fields.*.help_text' => ['nullable', 'string'],
            'fields.*.settings_json' => ['nullable'],
        ]);

        $template = FieldTemplate::create([
            'cooperative_id' => $this->activeCooperative()?->id,
            'created_by' => $request->user()?->id,
            'module' => $validated['module'] ?? null,
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'fields_json' => $validated['fields'],
        ]);

        return response()->json([
            'ok' => true,
            'template' => [
                'id' => $template->id,
                'name' => $template->name,
                'description' => $template->description,
                'module' => $template->module,
                'fieldCount' => count($template->fields_json ?? []),
                'fields' => $template->fields_json ?? [],
            ],
            'message' => 'Templat berjaya disimpan.',
        ]);
    }

    public function destroy(FieldTemplate $template): JsonResponse
    {
        $this->ensureSameCooperative($template);

        $template->delete();

        return response()->json([
            'ok' => true,
            'message' => 'Templat berjaya dipadam.',
        ]);
    }
}
