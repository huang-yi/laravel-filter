<?php

namespace HuangYi\Filter;

class Rule
{
    /**
     * Filter name.
     *
     * @var string
     */
    protected $name;

    /**
     * Filter value.
     *
     * @var mixed
     */
    protected $value;

    /**
     * Make a new rule.
     *
     * @param  string $name
     * @param  string $value
     * @return static
     */
    public static function make($name, $value)
    {
        return new static($name, $value);
    }

    /**
     * Rule.
     *
     * @param string $name
     * @param string $value
     * @return void
     */
    public function __construct($name, $value)
    {
        $this->name = $name;
        $this->value = $value;
    }

    /**
     * Get filter name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set filter name.
     *
     * @param  string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get filter value.
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set filter value.
     *
     * @param  mixed $value
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }
}
