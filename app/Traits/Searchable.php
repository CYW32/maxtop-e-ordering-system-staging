<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait Searchable
{
    public function scopeSearch(Builder $query, ?string $term = null)
    {
        // If no search term, do nothing
        if (! $term) {
            return $query;
        }

        // Search across the columns defined in the model
        $query->where(function ($q) use ($term) {
            foreach ($this->searchable as $field) {
                $q->orWhere($field, 'like', "%{$term}%");
            }
        });

        return $query;
    }
}
