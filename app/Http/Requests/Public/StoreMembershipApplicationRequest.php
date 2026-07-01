<?php

namespace App\Http\Requests\Public;

use App\Enums\MembershipApplicationStatus;
use App\Models\MembershipApplication;
use App\Models\Member;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreMembershipApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:255'],
            'identity_no' => ['required', 'string', 'max:30', 'regex:/^\d{6}-\d{2}-\d{4}$/'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:30', 'regex:/^01[0-9]-?\d{7,8}$/'],
            'address_line_1' => ['required', 'string', 'max:1000'],
            'city' => ['nullable', 'string', 'max:120'],
            'state' => ['nullable', 'string', 'max:120'],
            'postcode' => ['nullable', 'string', 'max:20'],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'gender' => ['required', Rule::in(['male', 'female'])],
            'occupation' => ['nullable', 'string', 'max:255'],
            'employer_name' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'referred_by_member_id' => ['nullable', 'integer', 'exists:members,id'],
            'digital_signature' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'full_name.required' => 'Nama penuh diperlukan.',
            'identity_no.required' => 'Nombor kad pengenalan diperlukan.',
            'identity_no.regex' => 'Sila masukkan nombor kad pengenalan yang sah (format: XXXXXX-XX-XXXX).',
            'email.required' => 'Alamat e-mel diperlukan.',
            'email.email' => 'Sila masukkan alamat e-mel yang sah.',
            'phone.required' => 'Nombor telefon diperlukan.',
            'phone.regex' => 'Sila masukkan nombor telefon yang sah (contoh: 0123456789).',
            'address_line_1.required' => 'Alamat diperlukan.',
            'date_of_birth.required' => 'Tarikh lahir diperlukan.',
            'date_of_birth.before' => 'Tarikh lahir mesti sebelum hari ini.',
            'gender.required' => 'Sila pilih jantina.',
            'gender.in' => 'Sila pilih jantina yang sah.',
            'digital_signature.required' => 'Tandatangan digital diperlukan.',
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                $identityNo = $this->input('identity_no');

                if (! $identityNo) {
                    return;
                }

                $normalized = str_replace('-', '', $identityNo);

                $existingMember = Member::query()
                    ->where(function ($q) use ($identityNo, $normalized) {
                        $q->where('identity_no', $identityNo)
                          ->orWhere('identity_no', $normalized);
                    })
                    ->exists();

                if ($existingMember) {
                    $validator->errors()->add(
                        'identity_no',
                        'Nombor kad pengenalan ini telah didaftarkan sebagai ahli. Sila <a href="/member/login" class="underline">log masuk ke Portal Ahli</a>.'
                    );

                    return;
                }

                $existingApplication = MembershipApplication::query()
                    ->where(function ($q) use ($identityNo, $normalized) {
                        $q->where('identity_no', $identityNo)
                          ->orWhere('identity_no', $normalized);
                    })
                    ->whereNotIn('status', [
                        MembershipApplicationStatus::Rejected->value,
                        MembershipApplicationStatus::Cancelled->value,
                    ])
                    ->exists();

                if ($existingApplication) {
                    $validator->errors()->add(
                        'identity_no',
                        'Nombor kad pengenalan ini sudah mempunyai permohonan yang sedang diproses.'
                    );
                }
            },
        ];
    }
}
