<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Link extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category_id',
        'title',
        'url',
        'description',
        'icon',
        'icon_type',
        'icon_url',
        'sort_order',
        'click_count',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'click_count' => 'integer',
        'icon_type' => 'string',
        'icon_url' => 'string',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'link_tag')->withTimestamps();
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeWithCategory(Builder $query): Builder
    {
        return $query->with('category');
    }

    public function incrementClickCount(): void
    {
        $this->increment('click_count');
    }
}
