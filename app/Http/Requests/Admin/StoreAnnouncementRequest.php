<?php

namespace App\Http\Requests\Admin;

use App\Enums\AnnouncementAudience;
use App\Enums\AnnouncementPriority;
use Illuminate\Foundation\Http\FormRequest;

class StoreAnnouncementRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'summary' => ['nullable', 'string', 'max:500'],
            'content' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:2048'],
            'audience' => ['required', 'string', 'in:' . implode(',', AnnouncementAudience::values())],
            'priority' => ['required', 'string', 'in:' . implode(',', AnnouncementPriority::values())],
            'send_via' => ['required', 'string', 'in:in_app,in_app,email'],
            'is_pinned' => ['boolean'],
            'published_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', 'after:published_at'],
            'target_user_ids' => ['nullable', 'array'],
            'target_user_ids.*' => ['integer', 'exists:users,id'],
        ];
    }
}
