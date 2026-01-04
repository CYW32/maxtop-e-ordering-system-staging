<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait DateFilterable
{
    /**
     * Filter query by a date range.
     *
     * @param  string  $column  (Default: created_at)
     * @return Builder
     */
    public function scopeFilterByDate(Builder $query, $column = 'created_at')
    {
        // 1. Check for Start Date
        if (request()->filled('start_date')) {
            $query->whereDate($column, '>=', request('start_date'));
        }

        // 2. Check for End Date
        if (request()->filled('end_date')) {
            $query->whereDate($column, '<=', request('end_date'));
        }

        return $query;
    }
}
