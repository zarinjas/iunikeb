<?php

namespace App\Http\Controllers\Member;

use App\Enums\FinancingApplicationStatus;
use App\Enums\AnnouncementAudience;
use App\Models\Announcement;
use App\Models\AnsuranProduct;
use App\Models\FinancingApplication;
use App\Models\FormSubmission;
use App\Models\MemberContribution;
use App\Models\MembershipApplication;
use App\Models\OnlineForm;
use App\Models\Banner;
use App\Models\Poster;
use App\Models\Program;
use App\Models\ProgramRsvp;
use App\Models\Survey;
use App\Services\MemberCardService;
use App\Services\Files\MemberPhotoStorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends MemberPortalController
{
    public function __construct(
        private readonly MemberPhotoStorageService $memberPhotos,
        private readonly MemberCardService $memberCards,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $user = $this->currentUser($request);
        $member = $this->currentMemberOrNull($request);
        $cooperativeId = $this->activeCooperativeId($request);
        $application = $member ? $this->latestApplication($member->id, $member->cooperative_id) : null;
        $financingSummary = $member ? $this->financingSummary($member) : null;
        $caruman = $member ? $this->carumanSummary($member, $cooperativeId) : null;

        $banners = Banner::query()
            ->where('cooperative_id', $cooperativeId)
            ->active()
            ->ordered()
            ->get()
            ->map(fn (Banner $b) => [
                'id' => $b->id,
                'title' => $b->image_path ? pathinfo($b->image_path, PATHINFO_FILENAME) : '',
                'alt_text' => '',
                'image_url' => $b->imageUrl(),
                'link_url' => $b->link_url,
            ])
            ->all();

        $posters = Poster::query()
            ->where('cooperative_id', $cooperativeId)
            ->active()
            ->ordered()
            ->get()
            ->map(fn (Poster $p) => [
                'id' => $p->id,
                'title' => $p->image_path ? pathinfo($p->image_path, PATHINFO_FILENAME) : '',
                'alt_text' => '',
                'image_url' => $p->imageUrl(),
                'link_url' => $p->link_url,
            ])
            ->all();

        $forms = OnlineForm::query()
            ->published()
            ->where('cooperative_id', $cooperativeId)
            ->with('category')
            ->where(function ($query) {
                $query->whereDoesntHave('category')
                    ->orWhereHas('category', fn ($q) => $q->where('is_active', true));
            })
            ->orderBy('sort_order')
            ->latest('updated_at')
            ->limit(4)
            ->get()
            ->map(fn (OnlineForm $form) => [
                'id' => $form->id,
                'title' => $form->title,
                'description' => $form->description,
                'category_name' => $form->category?->name,
                'visibility' => $form->visibility->value,
                'visibility_label' => $form->visibility->value === 'members_only' ? 'Ahli sahaja' : 'Terbuka',
                'updated_at' => $form->updated_at?->format('d/m/Y H:i'),
                'url' => route('public.forms.show', $form->slug),
            ])
            ->all();

        $recentSubmissions = $member
            ? FormSubmission::query()
                ->where('cooperative_id', $cooperativeId)
                ->where('member_id', $member->id)
                ->with('form.category')
                ->latest('submitted_at')
                ->limit(4)
                ->get()
                ->map(fn (FormSubmission $submission) => [
                    'id' => $submission->id,
                    'reference_no' => $submission->reference_no,
                    'status' => $submission->status->value,
                    'form_title' => $submission->form?->title,
                    'submitted_at' => $submission->submitted_at?->format('d/m/Y H:i'),
                    'detail_url' => route('member.applications.submissions.show', $submission),
                ])
                ->all()
            : [];

        $announcements = Announcement::query()
            ->forCooperative($cooperativeId)
            ->published()
            ->notExpired()
            ->where(function ($query) {
                $query->where('audience', AnnouncementAudience::Members->value)
                    ->orWhere('audience', AnnouncementAudience::Public->value);
            })
            ->orderByRaw('is_pinned DESC, published_at DESC')
            ->limit(5)
            ->get()
            ->map(fn (Announcement $a) => [
                'id' => $a->id,
                'title' => $a->title,
                'slug' => $a->slug,
                'summary' => $a->summary,
                'priority' => $a->priority,
                'is_pinned' => $a->is_pinned,
                'audience' => $a->audience,
                'published_at' => $a->published_at?->format('d/m/Y'),
                'show_url' => route('member.announcements.show', ['announcement' => $a->slug]),
            ])
            ->all();

        $upcomingPrograms = Program::query()
            ->forCooperative($cooperativeId)
            ->published()
            ->upcoming()
            ->limit(8)
            ->get()
            ->map(fn (Program $program) => [
                'id' => $program->id,
                'title' => $program->title,
                'description' => $program->description ? Str::limit(strip_tags((string) $program->description), 120) : null,
                'program_type' => $program->program_type,
                'location' => $program->location,
                'start_date_formatted' => $program->start_date?->format('j F Y'),
                'start_time' => $program->start_date?->format('g:i A'),
                'cover_image_url' => $program->cover_image_path ? Storage::disk('public')->url($program->cover_image_path) : null,
                'user_rsvp' => $member ? $this->memberProgramRsvp($program->id, $member->id) : null,
            ])
            ->all();

        $activeSurveys = Survey::query()
            ->forCooperative($cooperativeId)
            ->active()
            ->with('options')
            ->withCount('responses')
            ->latest()
            ->limit(3)
            ->get()
            ->map(fn (Survey $survey) => [
                'id' => $survey->id,
                'question' => $survey->question,
                'total_responses' => $survey->responses_count,
                'expires_at' => $survey->expires_at?->format('d/m/Y'),
                'has_voted' => $member ? $survey->responses()->where('member_id', $member->id)->exists() : false,
                'options' => $survey->options->sortBy('sort_order')->map(fn ($opt) => [
                    'id' => $opt->id,
                    'label' => $opt->label,
                ]),
            ])
            ->all();

        $ansuranProducts = AnsuranProduct::forCooperative($cooperativeId)
            ->with(['category', 'images', 'variants'])
            ->active()
            ->ordered()
            ->limit(8)
            ->get()
            ->map(fn (AnsuranProduct $p) => [
                'id' => $p->id,
                'name' => $p->name,
                'slug' => $p->slug,
                'category_name' => $p->category?->name,
                'primary_image_url' => $p->primaryImage()?->url(),
                'min_price' => $p->variants->min('price'),
                'max_price' => $p->variants->max('price'),
                'url' => route('member.ansuran.products.show', $p->slug),
            ])
            ->all();

        $profileFields = [
            'profile_photo_path' => $member?->profile_photo_path,
            'phone' => $member?->phone,
            'address_line_1' => $member?->address_line_1,
            'date_of_birth' => $member?->date_of_birth,
            'gender' => $member?->gender,
            'position' => $member?->position,
            'employer' => $member?->employer,
            'next_of_kin_name' => $member?->next_of_kin_name,
        ];

        $filledFields = collect($profileFields)->filter(fn ($v) => filled($v))->count();
        $totalFields = count($profileFields);
        $profileCompletionPercent = $totalFields > 0 ? (int) round(($filledFields / $totalFields) * 100) : 0;

        $onboardingCompleted = $member
            ? ($member->onboarding_completed_at !== null
                || ($member->profile_photo_path && $member->address_line_1 && $member->phone))
            : false;

        $missingFields = [];
        if ($member && ! $onboardingCompleted) {
            if (! $member->profile_photo_path) $missingFields[] = 'gambar profil';
            if (! $member->phone) $missingFields[] = 'nombor telefon';
            if (! $member->address_line_1) $missingFields[] = 'alamat';
        }

        return Inertia::render('Member/Pages/Dashboard', [
            'member' => [
                'is_linked' => (bool) $member,
                'member_no' => $member?->member_no,
                'profile_photo_url' => $this->memberPhotos->url($member?->profile_photo_path),
                'full_name' => $member?->full_name ?? $user->name,
                'membership_status' => $member?->membership_status->value ?? 'inactive',
                'joined_at' => $member?->joined_at?->format('d/m/Y'),
            ],
            'onboardingCompleted' => $onboardingCompleted,
            'profileCompletionPercent' => $profileCompletionPercent,
            'missingFields' => $missingFields,
            'digitalCard' => $member ? [
                ...$this->memberCards->memberPayload($member),
                'view_url' => route('member.card'),
            ] : null,
            'application' => $application ? [
                'application_no' => $application->application_no,
                'status' => $application->status->value,
                'submitted_at' => $application->submitted_at?->format('d/m/Y H:i'),
            ] : null,
            'quickActions' => [
                [
                    'label' => 'Kemaskini Profil',
                    'description' => 'Semak dan kemas kini maklumat hubungan anda.',
                    'href' => route('member.profile'),
                    'icon' => 'UserRound',
                ],
                [
                    'label' => 'Permohonan Borang',
                    'description' => 'Isi borang permohonan dan semak status permohonan anda.',
                    'href' => route('member.applications.index'),
                    'icon' => 'FileCheck',
                ],
                [
                    'label' => 'Mohon Pembiayaan Baru',
                    'description' => 'Semak produk pembiayaan yang tersedia dan mulakan permohonan baharu.',
                    'href' => route('member.financing.index'),
                    'icon' => 'HandCoins',
                ],
                [
                    'label' => 'Hantar Aduan',
                    'description' => 'Hantar aduan atau cadangan dan semak maklum balas admin.',
                    'href' => route('member.complaints.index'),
                    'icon' => 'MessagesSquare',
                ],
            ],
            'banners' => $banners,
            'posters' => $posters,
            'caruman' => $caruman,
            'featuredForms' => $forms,
            'recentSubmissions' => $recentSubmissions,
            'latestAnnouncements' => $announcements,
            'financingSummary' => $financingSummary,
            'ansuranProducts' => $ansuranProducts,
            'upcomingPrograms' => $upcomingPrograms,
            'activeSurveys' => $activeSurveys,
        ]);
    }

    private function carumanSummary(object $member, ?int $cooperativeId): ?array
    {
        $currentYear = (int) now()->format('Y');

        $availableYears = MemberContribution::query()
            ->forCooperative($cooperativeId)
            ->where('member_id', $member->id)
            ->pluck('year')
            ->unique()
            ->sortDesc()
            ->values();

        if ($availableYears->isEmpty()) {
            return null;
        }

        $year = $availableYears->contains($currentYear) ? $currentYear : $availableYears->first();

        $contribution = MemberContribution::query()
            ->forCooperative($cooperativeId)
            ->where('member_id', $member->id)
            ->year($year)
            ->first();

        if (! $contribution) {
            return null;
        }

        return [
            'id' => $contribution->id,
            'year' => $contribution->year,
            'caruman_semasa' => (float) $contribution->caruman_semasa,
            'caruman_keseluruhan' => (float) $contribution->caruman_keseluruhan,
            'dividen' => (float) $contribution->dividen,
            'available_years' => $availableYears->all(),
        ];
    }

    private function financingSummary(object $member): array
    {
        $underReview = FinancingApplication::query()
            ->where('member_id', $member->id)
            ->where('cooperative_id', $member->cooperative_id)
            ->whereIn('status', [
                FinancingApplicationStatus::Submitted->value,
                FinancingApplicationStatus::InReview->value,
                FinancingApplicationStatus::PendingGuarantor->value,
                FinancingApplicationStatus::PendingUpload->value,
                FinancingApplicationStatus::Incomplete->value,
            ])
            ->count();

        $guarantorRequests = $member->financingGuarantorRequests()
            ->where('status', 'pending')
            ->count();

        return [
            'under_review' => $underReview,
            'guarantor_requests' => $guarantorRequests,
        ];
    }

    private function latestApplication(int $memberId, int $cooperativeId): ?MembershipApplication
    {
        return MembershipApplication::query()
            ->where('cooperative_id', $cooperativeId)
            ->where('approved_member_id', $memberId)
            ->latest('submitted_at')
            ->first();
    }

    private function memberProgramRsvp(int $programId, int $memberId): ?array
    {
        $rsvp = ProgramRsvp::query()
            ->where('program_id', $programId)
            ->where('member_id', $memberId)
            ->first();

        if (! $rsvp) {
            return null;
        }

        return [
            'response' => $rsvp->response,
            'checked_in' => $rsvp->checked_in_at !== null,
        ];
    }
}