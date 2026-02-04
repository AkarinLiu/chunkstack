<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'color',
    ];

    public function links(): BelongsToMany
    {
        return $this->belongsToMany(Link::class, 'link_tag')->withTimestamps();
    }
}
