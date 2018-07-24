<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Filter Query Key
    |--------------------------------------------------------------------------
    |
    | Get filters from query string by this key.
    |
    */

    'key' => env('FILTER_KEY', 'filters'),

    /*
    |--------------------------------------------------------------------------
    | Log Level
    |--------------------------------------------------------------------------
    |
    | No logs if this value is set to null.
    |
    */

    'log_level' => env('FILTER_LOG_LEVEL', 'debug'),

    /*
    |--------------------------------------------------------------------------
    | Filter Parser Class
    |--------------------------------------------------------------------------
    |
    | The parser must implement 'HuangYi\Filter\Contracts\ParserContract'.
    |
    */

    'parser' => HuangYi\Filter\Parser::class,

];
