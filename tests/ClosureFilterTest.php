<?php

namespace HuangYi\Filter\Tests;

use HuangYi\Filter\ClosureFilter;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Mockery as m;
use PHPUnit\Framework\TestCase;

class ClosureFilterTest extends TestCase
{
    public function testMake()
    {
        $filter = ClosureFilter::make(function () {});

        $this->assertInstanceOf(ClosureFilter::class, $filter);
    }

    public function testApply()
    {
        $filter = ClosureFilter::make(function ($query, $value) {
            $this->assertInstanceOf(EloquentBuilder::class, $query);
            $this->assertEquals(1, $value);
        });

        $query = new EloquentBuilder(m::mock(QueryBuilder::class));

        $filter->apply($query, 1);
    }
}
