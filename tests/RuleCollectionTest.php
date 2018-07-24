<?php

namespace HuangYi\Filter\Tests;

use HuangYi\Filter\Rule;
use HuangYi\Filter\RuleCollection;
use PHPUnit\Framework\TestCase;

class RuleCollectionTest extends TestCase
{
    public function testPush()
    {
        $collection = new RuleCollection();

        $collection->push(new Rule('foo', 'bar'));

        $this->assertCount(1, $collection);
        $this->assertInstanceOf(Rule::class, $collection[0]);
    }
}
