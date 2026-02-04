<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'sort_order',
        'icon',
        'icon_type',
        'icon_url',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'icon_type' => 'string',
        'icon_url' => 'string',
    ];

    public function links(): HasMany
    {
        return $this->hasMany(Link::class)->orderBy('sort_order');
    }

    public function activeLinks(): HasMany
    {
        return $this->hasMany(Link::class)->active()->orderBy('sort_order');
    }
}
