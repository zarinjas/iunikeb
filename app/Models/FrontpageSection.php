<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FrontpageSection extends Model
{
    protected $fillable = [
        'cooperative_id',
        'key',
        'title',
        'subtitle',
        'data',
        'is_active',
    ];

    protected $casts = [
        'data' => 'array',
        'is_active' => 'boolean',
    ];

    public function items()
    {
        return $this->hasMany(FrontpageSectionItem::class, 'section_id')
            ->where('is_active', true)
            ->orderBy('sort_order');
    }

    public function allItems()
    {
        return $this->hasMany(FrontpageSectionItem::class, 'section_id')
            ->orderBy('sort_order');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByKey($query, string $key)
    {
        return $query->where('key', $key);
    }
}
