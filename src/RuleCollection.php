<?php

namespace HuangYi\Filter;

use ArrayAccess;
use ArrayIterator;
use Countable;
use Illuminate\Contracts\Support\Arrayable;
use IteratorAggregate;

class RuleCollection implements ArrayAccess, Arrayable, Countable, IteratorAggregate
{
    /**
     * Rules.
     *
     * @var array
     */
    protected $rules = [];

    /**
     * Push rule.
     *
     * @param \HuangYi\Filter\Rule $rule
     * @return $this
     */
    public function push(Rule $rule)
    {
        $this->rules[] = $rule;

        return $this;
    }

    /**
     * Determine if an item exists at an offset.
     *
     * @param  mixed  $key
     * @return bool
     */
    public function offsetExists($key)
    {
        return array_key_exists($key, $this->rules);
    }

    /**
     * Get an item at a given offset.
     *
     * @param  mixed  $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        return $this->rules[$key];
    }

    /**
     * Set the item at a given offset.
     *
     * @param  null $key
     * @param  \HuangYi\Filter\Rule $value
     * @return void
     */
    public function offsetSet($key, $value)
    {
        $this->rules[] = $value;
    }

    /**
     * Unset the item at a given offset.
     *
     * @param  string  $key
     * @return void
     */
    public function offsetUnset($key)
    {
        unset($this->rules[$key]);
    }

    /**
     * Get the filters as a array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->rules;
    }

    /**
     * Count elements
     *
     * @return int
     */
    public function count()
    {
        return count($this->rules);
    }

    /**
     * Get an iterator for the items.
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->rules);
    }
}
