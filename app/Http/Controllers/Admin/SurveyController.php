<?php

namespace App\Http\Controllers\Admin;

use App\Enums\SurveyStatus;
use App\Http\Controllers\Concerns\InteractsWithActiveCooperative;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSurveyRequest;
use App\Http\Requests\Admin\UpdateSurveyRequest;
use App\Models\Survey;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SurveyController extends Controller
{
    use InteractsWithActiveCooperative;

    public function __construct(
        private readonly AuditLogService $auditLog,
    ) {}

    public function index(Request $request): Response
    {
        $cooperative = $this->activeCooperative();

        $surveys = Survey::query()
            ->forCooperative($cooperative?->id)
            ->withCount('options', 'responses')
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(15)
            ->withQueryString()
            ->through(fn (Survey $survey) => [
                'id' => $survey->id,
                'question' => $survey->question,
                'status' => $survey->status,
                'options_count' => $survey->options_count,
                'responses_count' => $survey->responses_count,
                'expires_at' => $survey->expires_at?->format('d/m/Y H:i'),
                'created_at' => $survey->created_at?->format('d/m/Y H:i'),
            ]);

        return Inertia::render('Admin/Pages/Surveys/Index', [
            'surveys' => $surveys,
            'filters' => $request->only('status'),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Pages/Surveys/Form', [
            'survey' => null,
        ]);
    }

    public function store(StoreSurveyRequest $request): RedirectResponse
    {
        $cooperative = $this->activeCooperative();

        $survey = Survey::query()->create([
            'cooperative_id' => $cooperative?->id,
            'question' => $request->question,
            'description' => $request->description,
            'status' => SurveyStatus::Draft->value,
            'expires_at' => $request->expires_at,
            'created_by' => $request->user()?->id,
            'updated_by' => $request->user()?->id,
        ]);

        $options = collect($request->options)->map(fn (string $label, int $idx) => [
            'survey_id' => $survey->id,
            'label' => $label,
            'sort_order' => $idx,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $survey->options()->insert($options->toArray());

        $this->auditLog->record('survey.created', $survey, newValues: $survey->fresh()->load('options')->toArray());

        return redirect()->route('admin.surveys.index')->with('status', 'Undian berjaya dicipta.');
    }

    public function edit(Survey $survey): Response
    {
        $this->ensureSameCooperative($survey);

        return Inertia::render('Admin/Pages/Surveys/Form', [
            'survey' => [
                'id' => $survey->id,
                'question' => $survey->question,
                'description' => $survey->description,
                'status' => $survey->status,
                'expires_at' => $survey->expires_at?->format('Y-m-d\TH:i'),
                'options' => $survey->options->sortBy('sort_order')->values()->map(fn ($opt) => [
                    'id' => $opt->id,
                    'label' => $opt->label,
                ]),
            ],
        ]);
    }

    public function update(UpdateSurveyRequest $request, Survey $survey): RedirectResponse
    {
        $this->ensureSameCooperative($survey);

        $old = $survey->load('options')->toArray();

        $survey->update([
            'question' => $request->question,
            'description' => $request->description,
            'expires_at' => $request->expires_at,
            'status' => $request->status ?? $survey->status,
            'updated_by' => $request->user()?->id,
        ]);

        $survey->options()->delete();

        $options = collect($request->options)->map(fn (string $label, int $idx) => [
            'survey_id' => $survey->id,
            'label' => $label,
            'sort_order' => $idx,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $survey->options()->insert($options->toArray());

        $this->auditLog->record('survey.updated', $survey, oldValues: $old, newValues: $survey->fresh()->load('options')->toArray());

        return redirect()->route('admin.surveys.index')->with('status', 'Undian berjaya dikemas kini.');
    }

    public function show(Survey $survey): Response
    {
        $this->ensureSameCooperative($survey);

        $survey->load('options.responses');

        $totalResponses = $survey->responses()->count();

        $options = $survey->options->sortBy('sort_order')->map(fn ($opt) => [
            'id' => $opt->id,
            'label' => $opt->label,
            'count' => $opt->responses->count(),
            'percentage' => $totalResponses > 0 ? round(($opt->responses->count() / $totalResponses) * 100, 1) : 0,
        ]);

        return Inertia::render('Admin/Pages/Surveys/Show', [
            'survey' => [
                'id' => $survey->id,
                'question' => $survey->question,
                'description' => $survey->description,
                'status' => $survey->status,
                'expires_at' => $survey->expires_at?->format('d/m/Y H:i'),
                'total_responses' => $totalResponses,
                'created_at' => $survey->created_at?->format('d/m/Y H:i'),
            ],
            'options' => $options,
        ]);
    }

    public function publish(Survey $survey): RedirectResponse
    {
        $this->ensureSameCooperative($survey);

        $old = $survey->toArray();
        $survey->update(['status' => SurveyStatus::Published->value, 'updated_by' => request()->user()?->id]);

        $this->auditLog->record('survey.published', $survey, oldValues: $old, newValues: $survey->fresh()->toArray());

        return back()->with('status', 'Undian telah diterbitkan.');
    }

    public function close(Survey $survey): RedirectResponse
    {
        $this->ensureSameCooperative($survey);

        $old = $survey->toArray();
        $survey->update(['status' => SurveyStatus::Closed->value, 'updated_by' => request()->user()?->id]);

        $this->auditLog->record('survey.closed', $survey, oldValues: $old, newValues: $survey->fresh()->toArray());

        return back()->with('status', 'Undian telah ditutup.');
    }

    public function destroy(Survey $survey): RedirectResponse
    {
        $this->ensureSameCooperative($survey);

        $this->auditLog->record('survey.deleted', $survey, oldValues: $survey->load('options')->toArray());
        $survey->delete();

        return back()->with('status', 'Undian berjaya dipadam.');
    }
}
