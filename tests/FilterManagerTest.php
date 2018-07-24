<?php

namespace HuangYi\Filter\Tests;

use HuangYi\Filter\ClosureFilter;
use HuangYi\Filter\Contracts\FilterContract;
use HuangYi\Filter\Exceptions\InvalidFilterException;
use HuangYi\Filter\Exceptions\UndefinedFilterException;
use HuangYi\Filter\FilterManager;
use HuangYi\Filter\Parser;
use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Mockery as m;
use PHPUnit\Framework\TestCase;

class FilterManagerTest extends TestCase
{
    public function testNameClassFilter()
    {
        $manager = $this->getManager();

        $manager->name('foo', FooFilter::class);

        $this->assertEquals(['foo' => FooFilter::class], $manager->getNames());
    }

    public function testNameClosureFilter()
    {
        $manager = $this->getManager();

        $filter = function ($query, $value) {};

        $manager->name('foo', $filter);

        $this->assertEquals(['foo' => $filter], $manager->getNames());
    }

    public function testNameInvalidFilter()
    {
        $this->expectException(InvalidFilterException::class);

        $manager = $this->getManager();

        $manager->name('foo', 'InvalidFilter');
    }

    public function testFindClassFilter()
    {
        $manager = $this->getManager();

        $manager->name('foo', FooFilter::class);

        $filter = $manager->find('foo');

        $this->assertInstanceOf(FooFilter::class, $filter);
        $this->assertEquals($filter, $manager->getContainer()->make('filters.foo'));
    }

    public function testFindClosureFilter()
    {
        $manager = $this->getManager();

        $manager->name('foo', function () {});

        $filter = $manager->find('foo');

        $this->assertInstanceOf(ClosureFilter::class, $filter);
        $this->assertEquals($filter, $manager->getContainer()->make('filters.foo'));
    }

    public function testFindUndefinedFilter()
    {
        $this->expectException(UndefinedFilterException::class);

        $manager = $this->getManager();

        $manager->find('undefined');
    }

    public function testApply()
    {
        Parser::$skipValidation = true;

        $manager = $this->getManager();
        $manager->name('foo', FooFilter::class);

        $query = new EloquentBuilder(m::mock(QueryBuilder::class));

        $manager->apply($query);

        $this->assertTrue($query->applied);
    }

    protected function getManager()
    {
        $container = new Container();
        $request = new Request();
        $parser = new Parser($container);
        $config = new Repository(['filter' => ['key' => 'filters']]);

        $request->merge([
            'filters' => base64_encode(json_encode([
                [
                    'name' => 'foo',
                    'value' => 'bar',
                ],
            ])),
        ]);

        $container->instance('filter.parser', $parser);
        $container->instance('request', $request);
        $container->instance('config', $config);

        return new FilterManager($container);
    }
}

class FooFilter implements FilterContract
{
    public function apply(EloquentBuilder $query, $value)
    {
        $query->applied = true;
    }
}
