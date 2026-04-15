<?php

namespace App\Models\Traits;

trait HasSlugScope
{
    public function scopeFindBySlug($query, $slug)
    {
        return $query->where('slug', $slug);
    }
}
