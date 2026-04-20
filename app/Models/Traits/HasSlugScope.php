<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @template TModel of Model
 */
trait HasSlugScope
{
    /**
     * @param Builder<TModel> $query
     * @return Builder<TModel>
     */
    public function scopeFindBySlug(Builder $query, string $slug)
    {
        return $query->where('slug', $slug);
    }
}
