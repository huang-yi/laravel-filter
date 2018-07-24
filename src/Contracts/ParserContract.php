<?php

namespace HuangYi\Filter\Contracts;

use Illuminate\Http\Request;

interface ParserContract
{
    /**
     * Parse filter rules from request.
     *
     * @param \Illuminate\Http\Request $request
     * @return \HuangYi\Filter\RuleCollection
     */
    public function parse(Request $request);
}
