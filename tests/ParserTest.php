<?php

namespace HuangYi\Filter\Tests;

use HuangYi\Filter\Parser;
use HuangYi\Filter\Rule;
use HuangYi\Filter\RuleCollection;
use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;

class ParserTest extends TestCase
{
    public function testParse()
    {
        $request = $this->getRequest();

        $parser = new Parser($this->getContainer());

        $rules = $parser->skipValidation()->parse($request);

        $this->assertInstanceOf(RuleCollection::class, $rules);
        $this->assertEquals(2, count($rules));
        $this->assertInstanceOf(Rule::class, $rules[0]);
        $this->assertInstanceOf(Rule::class, $rules[1]);
        $this->assertEquals('user.name', $rules[0]->getName());
        $this->assertEquals('foo', $rules[0]->getValue());
        $this->assertEquals('user.gender', $rules[1]->getName());
        $this->assertEquals('male', $rules[1]->getValue());
    }

    protected function getContainer()
    {
        $container = new Container();
        $config = new Repository(['filter' => ['key' => 'filters']]);

        $container->instance('config', $config);

        return $container;
    }

    protected function getRequest()
    {
        $request = new Request;

        $request->merge([
            'filters' => base64_encode(json_encode([
                [
                    'name' => 'user.name',
                    'value' => 'foo',
                ],
                [
                    'name' => 'user.gender',
                    'value' => 'male',
                ],
            ])),
        ]);

        return $request;
    }
}
