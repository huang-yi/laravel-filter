<?php

namespace HuangYi\Filter;

use Closure;
use HuangYi\Filter\Contracts\FilterContract;
use HuangYi\Filter\Exceptions\InvalidFilterException;
use HuangYi\Filter\Exceptions\UndefinedFilterException;
use Illuminate\Contracts\Container\Container;
use Illuminate\Database\Eloquent\Builder;

class FilterManager
{
    /**
     * Filter names.
     *
     * @var array
     */
    protected $names = [];

    /**
     * Container.
     *
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $container;

    /**
     * Filter manager.
     *
     * @param \Illuminate\Contracts\Container\Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Name a filter.
     *
     * @param string $name
     * @param string $filter
     * @return void
     * @throws \HuangYi\Filter\Exceptions\InvalidFilterException
     */
    public function name($name, $filter)
    {
        $this->validateFilter($filter);

        $this->names[$name] = $filter;
    }

    /**
     * Validate filter.
     *
     * @param mixed $filter
     * @return void
     * @throws \HuangYi\Filter\Exceptions\InvalidFilterException
     */
    protected function validateFilter($filter)
    {
        if (is_subclass_of($filter, FilterContract::class)) {
            return;
        }

        if ($filter instanceof Closure) {
            return;
        }

        throw new InvalidFilterException(
            'The filter must be a class implements ['.FilterContract::class.'] or a closure.'
        );
    }

    /**
     * Get names.
     *
     * @return array
     */
    public function getNames()
    {
        return $this->names;
    }

    /**
     * Apply filters.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return void
     * @throws \HuangYi\Filter\Exceptions\UndefinedFilterException
     */
    public function apply(Builder $query)
    {
        $rules = $this->container['filter.parser']->parse(
            $this->container['request']
        );

        foreach ($rules as $rule) {
            $filter = $this->find($rule->getName());

            $filter->apply($query, $rule->getValue());
        }
    }

    /**
     * Find filter.
     *
     * @param string $name
     * @return \HuangYi\Filter\Contracts\FilterContract
     * @throws \HuangYi\Filter\Exceptions\UndefinedFilterException
     */
    public function find($name)
    {
        $abstract = $this->getFilterInstanceAbstract($name);

        if ($this->container->has($abstract)) {
            return $this->container->make($abstract);
        }

        return $this->createFilter($name);
    }

    /**
     * Create a filter.
     *
     * @param string $name
     * @return \HuangYi\Filter\Contracts\FilterContract
     * @throws \HuangYi\Filter\Exceptions\UndefinedFilterException
     */
    protected function createFilter($name)
    {
        if (! isset($this->names[$name])) {
            throw new UndefinedFilterException($name);
        }

        $resolver = $this->names[$name];

        if ($resolver instanceof Closure) {
            $filter = ClosureFilter::make($resolver);
        } else {
            $filter = $this->container->make($resolver);
        }

        $abstract = $this->getFilterInstanceAbstract($name);

        $this->container->instance($abstract, $filter);

        return $filter;
    }

    /**
     * Get filter instance abstract.
     *
     * @param string $name
     * @return string
     */
    protected function getFilterInstanceAbstract($name)
    {
        return 'filters.'.$name;
    }

    /**
     * Get container.
     *
     * @return \Illuminate\Contracts\Container\Container
     */
    public function getContainer()
    {
        return $this->container;
    }
}
