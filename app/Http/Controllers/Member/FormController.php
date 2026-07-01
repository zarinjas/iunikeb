<?php

namespace App\Http\Controllers\Member;

use App\Enums\FormStatus;
use App\Http\Requests\Public\StoreOnlineFormSubmissionRequest;
use App\Models\FormCategory;
use App\Models\OnlineForm;
use App\Services\Forms\FormSubmissionService;
use App\Services\MemberFormAutofillService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FormController extends MemberPortalController
{
    public function __construct(
        private readonly FormSubmissionService $submissions,
        private readonly MemberFormAutofillService $autofill,
    ) {}

    public function index(Request $request): Response
    {
        $search = trim((string) $request->string('search'));
        $cooperativeId = $this->activeCooperativeId($request);

        $categories = FormCategory::query()
            ->where('cooperative_id', $cooperativeId)
            ->active()
            ->withCount(['forms as published_forms_count' => fn ($query) => $query->published()])
            ->latest()
            ->get()
            ->map(fn (FormCategory $category) => [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'description' => $category->description,
                'icon' => $category->icon,
                'published_forms_count' => $category->published_forms_count,
                'url' => route('member.forms.index').'?category='.$category->slug,
            ])
            ->all();

        $featuredForms = OnlineForm::query()
            ->where('cooperative_id', $cooperativeId)
            ->published()
            ->where(function ($query) {
                $query->whereDoesntHave('category')
                    ->orWhereHas('category', fn ($q) => $q->where('is_active', true));
            })
            ->when($search !== '', fn ($query) => $query->where('title', 'like', "%{$search}%"))
            ->with('category')
            ->latest('updated_at')
            ->limit(12)
            ->get()
            ->map(fn (OnlineForm $form) => $this->serializeCard($form))
            ->all();

        return Inertia::render('Member/Pages/Forms/Index', [
            'filters' => ['search' => $search],
            'categories' => $categories,
            'featuredForms' => $featuredForms,
        ]);
    }

    public function show(OnlineForm $onlineForm): Response|RedirectResponse
    {
        $user = request()->user();
        abort_if($onlineForm->status !== FormStatus::Published, 404);
        abort_unless($onlineForm->cooperative_id === $this->activeCooperativeId(request()), 404);

        $onlineForm->load([
            'category',
            'sections' => fn ($query) => $query->where('is_active', true)->latest()->orderBy('id'),
            'sections.fields' => fn ($query) => $query->where('is_active', true)->latest()->orderBy('id'),
        ]);

        $defaultInstruction = 'Borang ini perlu dicetak dan mendapatkan tandatangan serta cop pengesahan sebelum dimuat naik semula.';

        $autofillData = [];
        $member = $user?->member;
        if ($member) {
            $autofillData = $this->autofill->build($member);
        }

        return Inertia::render('Member/Pages/Forms/Show', [
            'formRecord' => [
                'id' => $onlineForm->id,
                'title' => $onlineForm->title,
                'slug' => $onlineForm->slug,
                'description' => $onlineForm->description,
                'visibility' => $onlineForm->visibility->value,
                'visibility_label' => 'Ahli sahaja',
                'status' => $onlineForm->status->value,
                'success_message' => $onlineForm->success_message ?: 'Borang anda berjaya dihantar.',
                'submission_method' => $onlineForm->submission_method->value,
                'stamped_upload_instructions' => $onlineForm->stamped_upload_instructions ?: $defaultInstruction,
                'show_document_header' => $onlineForm->show_document_header,
                'document_code' => $onlineForm->document_code,
                'revision_no' => $onlineForm->revision_no,
                'effective_date' => $onlineForm->effective_date?->format('d/m/Y'),
                'document_title' => $onlineForm->document_title,
                'category_name' => $onlineForm->category?->name,
                'sections' => $onlineForm->sections->map(function ($section) {
                    return [
                        'id' => $section->id,
                        'title' => $section->title,
                        'description' => $section->description,
                        'page_break_before' => $section->page_break_before,
                        'fields' => $section->fields->map(function ($field) {
                            return [
                                'id' => $field->id,
                                'label' => $field->label,
                                'field_key' => $field->field_key,
                                'type' => $field->type->value,
                                'placeholder' => $field->placeholder,
                                'help_text' => $field->help_text,
                                'is_required' => $field->is_required,
                                'options' => $field->options_json ?? [],
                                'display_mode' => $field->displayMode()->value,
                                'settings_json' => $field->settings_json ?? [],
                                'validation_json' => $field->validation_json ?? [],
                            ];
                        })->all(),
                    ];
                })->all(),
            ],
            'autofillData' => $autofillData,
        ]);
    }

    public function store(StoreOnlineFormSubmissionRequest $request, OnlineForm $onlineForm): RedirectResponse
    {
        abort_if($onlineForm->status !== FormStatus::Published, 404);

        $member = $request->user()?->member;
        $submission = $this->submissions->submit(
            $onlineForm->load('fields'),
            $request->validated(),
            $request->user(),
            $member,
        );

        session()->put("form_submission.{$submission->id}", true);

        return redirect()->route('member.applications.submissions.show', $submission)
            ->with('status', 'Borang anda berjaya dihantar. Rujukan: '.$submission->reference_no.'.');
    }

    private function serializeCard(OnlineForm $form): array
    {
        return [
            'id' => $form->id,
            'title' => $form->title,
            'slug' => $form->slug,
            'description' => $form->description,
            'category_name' => $form->category?->name,
            'visibility' => $form->visibility->value,
            'visibility_label' => 'Ahli sahaja',
            'url' => route('member.forms.show', $form->slug),
        ];
    }
}
