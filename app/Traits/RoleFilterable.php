<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait RoleFilterable
{
    /**
     * Filter query by Spatie Role name.
     */
    public function scopeFilterByRole(Builder $query)
    {
        // Check if 'role' is in the URL (e.g. ?role=admin)
        if (request()->filled('role')) {

            // Query the relationship
            return $query->whereHas('roles', function ($q) {
                $q->where('name', request('role'));
            });
        }

        return $query;
    }
}
