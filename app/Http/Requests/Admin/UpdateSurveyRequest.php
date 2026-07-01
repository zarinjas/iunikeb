<?php

namespace App\Http\Requests\Admin;

use App\Support\AccessControl;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSurveyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can(AccessControl::PERMISSION_MANAGE_SURVEYS) ?? false;
    }

    public function rules(): array
    {
        return [
            'question' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'expires_at' => ['nullable', 'date'],
            'status' => ['nullable', 'string', 'in:draft,published,closed'],
            'options' => ['required', 'array', 'min:2'],
            'options.*' => ['required', 'string', 'max:255', 'distinct'],
        ];
    }

    public function messages(): array
    {
        return [
            'question.required' => 'Soalan undian wajib diisi.',
            'question.max' => 'Soalan undian maksimum 255 aksara.',
            'description.max' => 'Penerangan maksimum 2000 aksara.',
            'options.required' => 'Sekurang-kurangnya 2 pilihan diperlukan.',
            'options.min' => 'Sekurang-kurangnya 2 pilihan diperlukan.',
            'options.*.required' => 'Setiap pilihan wajib diisi.',
            'options.*.max' => 'Setiap pilihan maksimum 255 aksara.',
            'options.*.distinct' => 'Pilihan tidak boleh sama.',
        ];
    }
}
