<?php
require_once __DIR__ . '/Functional.php';

use Yuyat\Functional\WithIterator as F;

class TestCase extends \PHPUnit_Framework_TestCase
{
    public function test_range()
    {
        $this->assertIterator([1, 2, 3, 4, 5], F\range(1, 5));
        $this->assertIterator([1, 3, 5, 7, 9], F\range(1, 10, 2));
    }

    public function test_map()
    {
        $this->assertIterator([1, 4, 9, 16, 25], F\map('power', F\range(1, 5)));
    }

    public function test_filter()
    {
        $this->assertIterator([1, 3, 5], F\filter('odd', F\range(1, 5)));
    }

    public function test_not()
    {
        $this->assertIterator([2, 4], F\filter(F\not('odd'), F\range(1, 5)));
    }

    public function test_head()
    {
        $this->assertSame(1, F\head(F\range(1, \INF)));
    }

    public function test_tail()
    {
        $this->assertIterator([2, 3, 4, 5], F\tail(F\range(1, 5)));
    }

    public function test_any()
    {
        $this->assertTrue(F\any('odd', new ArrayIterator([2, 4, 5])));
        $this->assertFalse(F\any('odd', new ArrayIterator([2, 4, 6])));
    }

    public function test_all()
    {
        $this->assertTrue(F\all('odd', new ArrayIterator([1, 3, 5])));
        $this->assertFalse(F\all('odd', new ArrayIterator([1, 3, 6])));
    }

    public function test_take()
    {
        $this->assertIterator([1, 2, 3, 4, 5], F\take(5, F\range(1, \INF)));
        $this->assertIterator([], F\take(0, F\range(1, \INF)));
    }

    public function test_takeWhile()
    {
        $this->assertIterator([1, 2, 3, 4, 5, 6, 7, 8, 9], F\takeWhile(function ($x) { return $x < 10; }, F\range(1, INF)));
    }

    public function test_dropWhile()
    {
        $this->assertIterator([6, 7, 8, 9, 10], F\dropWhile(function ($x) { return $x < 6; }, F\range(1, 10)));
    }

    public function test_foldl()
    {
        $this->assertSame(55, F\foldl(F\op('+'), 0, F\range(1, 10)));
    }

    public function test_foldl1()
    {
        $this->assertSame(55, F\foldl1(F\op('+'), F\range(1, 10)));
    }

    public function test_op_plus()
    {
        $fn = F\op('+');

        $this->assertSame(3, $fn(1, 2));
        $this->assertSame(6, $fn(1, 2, 3));
    }

    public function assertIterator($expected, Traversable $actualIterator)
    {
        $this->assertSame($expected, \iterator_to_array($actualIterator));
    }
}

function power($x) {
    return $x * $x;
}

function odd($x) {
    return $x % 2 === 1;
}
