<?php

namespace App\Http\Controllers\Member;

use App\Models\Cooperative;
use App\Models\Member;
use App\Models\MemberContribution;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Storage;
use Inertia\Response;

class CarumanController extends MemberPortalController
{
    public function index(Request $request): Response
    {
        $cooperativeId = $this->activeCooperativeId($request);
        $member = $this->currentMemberOrNull($request);

        $year = (int) $request->input('year', (int) now()->format('Y'));

        $contribution = MemberContribution::query()
            ->forCooperative($cooperativeId)
            ->where('member_id', $member?->id)
            ->year($year)
            ->first();

        $allYears = MemberContribution::query()
            ->forCooperative($cooperativeId)
            ->where('member_id', $member?->id)
            ->pluck('year')
            ->unique()
            ->sort()
            ->values();

        if ($allYears->isEmpty()) {
            $allYears = collect([$year]);
        } elseif (! $allYears->contains($year)) {
            $allYears->push($year);
        }

        return inertia('Member/Pages/Caruman/Index', [
            'member' => $this->serializeMemberForMemberPage($member),
            'contribution' => $contribution ? $this->serializeContribution($contribution) : null,
            'years' => $allYears->values(),
            'selectedYear' => $year,
        ]);
    }

    public function statement(Request $request): HttpResponse
    {
        $cooperativeId = $this->activeCooperativeId($request);
        $member = $this->currentMember($request);

        $year = (int) $request->input('year', (int) now()->format('Y'));

        $contribution = MemberContribution::query()
            ->forCooperative($cooperativeId)
            ->where('member_id', $member->id)
            ->year($year)
            ->first();

        abort_unless($contribution, 404, 'Data caruman tidak tersedia untuk tahun ' . $year);

        $cooperative = Cooperative::find($cooperativeId);

        $cooperativeData = [
            'name' => $cooperative?->name,
            'registration_no' => $cooperative?->registration_no,
            'logo_url' => $cooperative?->logo_path ? Storage::disk('public')->url($cooperative->logo_path) : null,
        ];

        $memberData = [
            'full_name' => $member->full_name,
            'member_no' => $member->member_no,
        ];

        $contributionData = [
            'caruman_semasa' => (float) $contribution->caruman_semasa,
            'caruman_keseluruhan' => (float) $contribution->caruman_keseluruhan,
            'dividen' => (float) $contribution->dividen,
        ];

        $pdf = Pdf::loadView('caruman.statement-pdf', [
            'cooperative' => $cooperativeData,
            'member' => $memberData,
            'contribution' => $contributionData,
            'year' => $year,
        ]);

        $pdf->setPaper('A4');
        $pdf->setOptions([
            'dpi' => 120,
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
        ]);

        $filename = 'Penyata_Caruman_' . $year . '_' . $member->member_no . '.pdf';

        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    private function serializeContribution(MemberContribution $contribution): array
    {
        return [
            'id' => $contribution->id,
            'year' => $contribution->year,
            'caruman_semasa' => (float) $contribution->caruman_semasa,
            'caruman_keseluruhan' => (float) $contribution->caruman_keseluruhan,
            'dividen' => (float) $contribution->dividen,
        ];
    }

    private function serializeMemberForMemberPage(?Member $member): ?array
    {
        if (! $member) {
            return null;
        }

        return [
            'id' => $member->id,
            'member_no' => $member->member_no,
            'full_name' => $member->full_name,
            'membership_status' => $member->membership_status,
        ];
    }
}