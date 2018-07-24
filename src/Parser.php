<?php

namespace HuangYi\Filter;

use HuangYi\Filter\Contracts\ParserContract;
use Illuminate\Contracts\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class Parser implements ParserContract
{
    /**
     * Container.
     *
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $container;

    /**
     * Filter resolvers.
     *
     * @var array
     */
    protected $resolvers;

    /**
     * If skip validation.
     *
     * @var bool
     */
    public static $skipValidation = false;

    /**
     * Filter parser.
     *
     * @param \Illuminate\Contracts\Container\Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Parse filter rules from request.
     *
     * @param \Illuminate\Http\Request $request
     * @return \HuangYi\Filter\RuleCollection
     * @throws \Illuminate\Validation\ValidationException
     */
    public function parse(Request $request)
    {
        $ruleCollection = new RuleCollection;

        $key = $this->container['config']['filter.key'];

        if (! $content = $request->query($key)) {
            return $ruleCollection;
        }

        $rules = $this->parseRules($key, $content);

        $this->logRules($request, $key, $rules);

        foreach ($rules as $rule) {
            $ruleCollection->push(
                Rule::make($rule['name'], $rule['value'])
            );
        }

        return $ruleCollection;
    }

    /**
     * Parse rules.
     *
     * @param string $key
     * @param string $content
     * @return array
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function parseRules($key, $content)
    {
        // Filter rules are encoded in base64.
        $decodedContent = base64_decode($content);

        if ($decodedContent === false) {
            throw ValidationException::withMessages([
                $key => "The '$key' is invalid.",
            ]);
        }

        $rules = json_decode($decodedContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw ValidationException::withMessages([
                $key => "The '$key' is invalid.",
            ]);
        }

        $this->validateRules($key, $rules);

        return $rules;
    }

    /**
     * Validate rules.
     *
     * @param string $key
     * @param array $rules
     * @return void
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateRules($key, $rules)
    {
        if (static::$skipValidation) {
            return;
        }

        $validator = $this->container['validator']->make([$key => $rules], [
            $key => 'array',
            "$key.*.name" => 'required',
            "$key.*.value" => 'present',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    /**
     * Log rules.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $key
     * @param array $rules
     * @return void
     */
    protected function logRules(Request $request, $key, array $rules)
    {
        $level = $this->container['config']['filter.log_level'];

        if (! $level || ! $this->container->has('log')) {
            return;
        }

        $message = sprintf(
            "%s %s\n%s",
            $request->method(),
            $request->fullUrl(),
            json_encode([$key => $rules])
        );

        $this->container['log']->log($level, $message);
    }

    /**
     * Skip validation.
     *
     * @return $this
     */
    public function skipValidation()
    {
        static::$skipValidation = true;

        return $this;
    }
}
