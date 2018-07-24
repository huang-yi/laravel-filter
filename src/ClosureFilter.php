<?php

namespace HuangYi\Filter;

use Closure;
use HuangYi\Filter\Contracts\FilterContract;
use Illuminate\Database\Eloquent\Builder;

class ClosureFilter implements FilterContract
{
    /**
     * @var \Closure
     */
    protected $closure;

    /**
     * Make a ClosureFilter.
     *
     * @param \Closure $closure
     * @return static
     */
    public static function make(Closure $closure)
    {
        return new static($closure);
    }

    /**
     * ClosureFilter.
     *
     * @param \Closure $closure
     */
    public function __construct(Closure $closure)
    {
        $this->closure = $closure;
    }

    /**
     * Apply filter to eloquent builder.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $value
     * @return void
     */
    public function apply(Builder $query, $value)
    {
        call_user_func($this->closure, $query, $value);
    }
}
