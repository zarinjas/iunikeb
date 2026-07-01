<?php

namespace App\Http\Controllers\Member;

use App\Models\Survey;
use App\Models\SurveyResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SurveyController extends MemberPortalController
{
    public function index(Request $request): Response
    {
        $cooperativeId = $this->activeCooperativeId($request);
        $member = $this->currentMemberOrNull($request);

        $surveys = Survey::query()
            ->forCooperative($cooperativeId)
            ->where('status', 'published')
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->with('options')
            ->withCount('responses')
            ->latest()
            ->paginate(15)
            ->withQueryString()
            ->through(fn (Survey $survey) => [
                'id' => $survey->id,
                'question' => $survey->question,
                'description' => $survey->description,
                'total_responses' => $survey->responses_count,
                'expires_at' => $survey->expires_at?->format('d/m/Y'),
                'has_voted' => $member ? $survey->responses()->where('member_id', $member->id)->exists() : false,
                'options' => $survey->options->sortBy('sort_order')->map(fn ($opt) => [
                    'id' => $opt->id,
                    'label' => $opt->label,
                ]),
            ]);

        return Inertia::render('Member/Pages/Surveys/Index', [
            'surveys' => $surveys,
        ]);
    }

    public function show(Request $request, Survey $survey): Response
    {
        $cooperativeId = $this->activeCooperativeId($request);
        abort_if($survey->cooperative_id !== $cooperativeId, 404);
        abort_if($survey->status !== 'published', 404);

        $member = $this->currentMemberOrNull($request);
        $hasVoted = $member ? $survey->responses()->where('member_id', $member->id)->exists() : false;

        $survey->load('options');

        return Inertia::render('Member/Pages/Surveys/Show', [
            'survey' => [
                'id' => $survey->id,
                'question' => $survey->question,
                'description' => $survey->description,
                'expires_at' => $survey->expires_at?->format('d/m/Y'),
                'total_responses' => $survey->total_responses,
            ],
            'options' => $survey->options->sortBy('sort_order')->values()->map(fn ($opt) => [
                'id' => $opt->id,
                'label' => $opt->label,
            ]),
            'hasVoted' => $hasVoted,
        ]);
    }

    public function store(Request $request, Survey $survey): RedirectResponse
    {
        $cooperativeId = $this->activeCooperativeId($request);
        abort_if($survey->cooperative_id !== $cooperativeId, 404);
        abort_if($survey->status !== 'published', 404);

        $member = $this->currentMember($request);

        $validated = $request->validate([
            'survey_option_id' => ['required', 'exists:survey_options,id'],
        ], [
            'survey_option_id.required' => 'Sila pilih satu pilihan.',
            'survey_option_id.exists' => 'Pilihan tidak sah.',
        ]);

        $option = $survey->options()->findOrFail($validated['survey_option_id']);

        $exists = SurveyResponse::query()
            ->where('survey_id', $survey->id)
            ->where('member_id', $member->id)
            ->exists();

        if ($exists) {
            return back()->with('status', 'Anda sudah mengundi undian ini.');
        }

        SurveyResponse::query()->create([
            'survey_id' => $survey->id,
            'survey_option_id' => $option->id,
            'member_id' => $member->id,
        ]);

        $survey->increment('total_responses');

        return back()->with('status', 'Undian anda telah direkodkan. Terima kasih!');
    }
}
