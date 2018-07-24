<?php

namespace HuangYi\Filter;

use Illuminate\Database\Eloquent\Builder;

trait HasFilter
{
    /**
     * Apply filters to eloquent builder.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return void
     */
    public function applyFilters(Builder $query)
    {
        app('filter')->apply($query);
    }
}
