<?php

namespace HuangYi\Filter\Exceptions;

class UndefinedFilterException extends FilterException
{
    /**
     * UndefinedFilterException.
     *
     * @param string $name
     */
    public function __construct($name)
    {
        $message = "Filter [$name] is undefined.";

        parent::__construct($message);
    }
}
