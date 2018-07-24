<?php

namespace HuangYi\Filter\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Filter facade
 *
 * @method static void name(string $name, string|\Closure $filter)
 * @method static void apply(\Illuminate\Database\Eloquent\Builder $query)
 * @method static \HuangYi\Filter\Contracts\FilterContract find(string $name)
 */
class Filter extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'filter';
    }
}
