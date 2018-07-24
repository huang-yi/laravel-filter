<?php

namespace HuangYi\Filter\Contracts;

use Illuminate\Database\Eloquent\Builder;

interface FilterContract
{
    /**
     * Apply filter to eloquent builder.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $value
     * @return void
     */
    public function apply(Builder $query, $value);
}
