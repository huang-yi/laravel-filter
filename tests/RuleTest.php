<?php

namespace HuangYi\Filter\Tests;

use HuangYi\Filter\Rule;
use PHPUnit\Framework\TestCase;

class RuleTes extends TestCase
{
    public function testMake()
    {
        $rule = Rule::make('foo', 'bar');

        $this->assertInstanceOf(Rule::class, $rule);
    }

    public function testGetName()
    {
        $rule = Rule::make('foo', 'bar');

        $this->assertEquals('foo', $rule->getName());
    }

    public function testSetName()
    {
        $rule = Rule::make('foo', 'bar');

        $rule->setName('foo1');

        $this->assertEquals('foo1', $rule->getName());
    }

    public function testGetValue()
    {
        $rule = Rule::make('foo', 'bar');

        $this->assertEquals('bar', $rule->getValue());
    }

    public function testSetValue()
    {
        $rule = Rule::make('foo', 'bar');

        $rule->setValue('bar1');

        $this->assertEquals('bar1', $rule->getValue());
    }
}
