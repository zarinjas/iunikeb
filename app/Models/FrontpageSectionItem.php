<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FrontpageSectionItem extends Model
{
    protected $fillable = [
        'section_id',
        'title',
        'subtitle',
        'description',
        'image',
        'icon',
        'value',
        'button_text',
        'button_url',
        'extra',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'extra' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function section()
    {
        return $this->belongsTo(FrontpageSection::class, 'section_id');
    }

    public function imageUrl(): ?string
    {
        if (! $this->image) {
            return null;
        }

        if (str_starts_with($this->image, 'http')) {
            return $this->image;
        }

        return url('storage/' . $this->image);
    }
}
