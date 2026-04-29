<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Link extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category_id',
        'title',
        'slug',
        'url',
        'description',
        'icon',
        'icon_type',
        'icon_url',
        'sort_order',
        'page_view_count',
        'click_count',
        'is_active',
    ];

    protected $attributes = [
        'is_active' => true,
        'sort_order' => 0,
        'page_view_count' => 0,
        'click_count' => 0,
        'icon_type' => 'emoji',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'page_view_count' => 'integer',
        'click_count' => 'integer',
        'icon_type' => 'string',
        'icon_url' => 'string',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Link $link): void {
            $link->slug ??= static::generateUniqueSlug($link->title);
        });

        static::updating(function (Link $link): void {
            if ($link->isDirty('title') && ! $link->isDirty('slug')) {
                $link->slug = static::generateUniqueSlug($link->title);
            }
        });
    }

    private static function generateUniqueSlug(string $title): string
    {
        $slug = Str::slug($title);
        $original = $slug;
        $count = 1;

        while (static::withTrashed()->where('slug', $slug)->whereNotNull('slug')->exists()) {
            $slug = $original.'-'.$count;
            $count++;
        }

        return $slug;
    }

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

    public function incrementPageViewCount(): void
    {
        $this->increment('page_view_count');
    }

    public function incrementClickCount(): void
    {
        $this->increment('click_count');
    }
}
