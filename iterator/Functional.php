<?php
namespace Yuyat\Functional\WithIterator;

use ArrayIterator;
use Iterator;
use IteratorIterator;
use Traversable;

class RangeIterator implements Iterator
{
    private $start;
    private $end;
    private $step;
    private $key;
    private $current;

    public function __construct($start, $end, $step = 1)
    {
        $this->start = $start;
        $this->end   = $end;
        $this->step  = $step;
    }

    public function rewind()
    {
        $this->key     = 0;
        $this->current = $this->start;
    }

    public function next()
    {
        $this->key += 1;
        $this->current += $this->step;
    }

    public function key()
    {
        return $this->key;
    }

    public function current()
    {
        return $this->current;
    }

    public function valid()
    {
        return $this->current() <= $this->end;
    }
}

class MapIterator extends IteratorIterator
{
    private $fn;

    public function __construct(Traversable $iterator, callable $fn)
    {
        $this->fn = $fn;

        parent::__construct($iterator);
    }

    public function current()
    {
        return call_user_func($this->fn, parent::current());
    }
}

class FilterIterator extends IteratorIterator
{
    private $fn;

    private $key;

    public function __construct(Traversable $iterator, $fn)
    {
        $this->fn = $fn;

        parent::__construct($iterator);
    }

    public function rewind()
    {
        $this->key = 0;
        parent::rewind();
        $this->skip();
    }

    public function next()
    {
        parent::next();
        $this->key += 1;

        while ($this->valid()) {
            if (call_user_func($this->fn, $this->current())) {
                return;
            }

            parent::next();
        }
    }

    public function key()
    {
        return $this->key;
    }

    private function skip()
    {
        while ($this->valid()) {
            if (call_user_func($this->fn, $this->current())) {
                return;
            }

            parent::next();
        }
    }
}

class TailIterator extends IteratorIterator
{
    public function key()
    {
        return parent::key() - 1;
    }

    public function rewind()
    {
        parent::rewind();
        $this->next();
    }
}

class TakeWhileIterator extends IteratorIterator
{
    private $fn;

    public function __construct(Traversable $iterator, $fn)
    {
        $this->fn = $fn;
        parent::__construct($iterator);
    }

    public function valid()
    {
        return call_user_func($this->fn, $this->current());
    }
}

class DropWhileIterator extends IteratorIterator
{
    private $fn;

    private $key;

    public function __construct(Traversable $iterator, $fn)
    {
        $this->fn = $fn;
        parent::__construct($iterator);
    }

    public function rewind()
    {
        parent::rewind();

        $this->key = 0;

        while (call_user_func($this->fn, $this->current())) {
            parent::next();
        }
    }

    public function next()
    {
        $this->key += 1;

        parent::next();
    }

    public function key()
    {
        return $this->key;
    }
}

function range($start, $end, $step = 1) {
    return new RangeIterator($start, $end, $step);
}

function map(callable $fn, $iterator) {
    return new MapIterator($iterator, $fn);
}

function filter(callable $fn, $iterator) {
    return new FilterIterator($iterator, $fn);
}

function not(callable $fn) {
    return function ($x) use ($fn) {
        return !$fn($x);
    };
}

function head($iterator) {
    foreach ($iterator as $value) {
        return $value;
    }
}

function tail($iterator) {
    return new TailIterator($iterator);
}

function any($fn, $iterator) {
    foreach ($iterator as $value) {
        if ($fn($value)) {
            return true;
        }
    }

    return false;
}

function all($fn, $iterator) {
    foreach ($iterator as $value) {
        if (!$fn($value)) {
            return false;
        }
    }

    return true;
}

function takeWhile($fn, $iterator) {
    return new TakeWhileIterator($iterator, $fn);
}

function dropWhile($fn, $iterator) {
    return new dropWhileIterator($iterator, $fn);
}

function foldl($fn, $initial, $iterator) {
    $a = $initial;

    foreach ($iterator as $value) {
        $b = $value;
        $a = $fn($a, $b);
    }

    return $a;
}

function foldl1($fn, $iterator) {
    $a = head($iterator);

    foreach (tail($iterator) as $value) {
        $b = $value;
        $a = $fn($a, $b);
    }

    return $a;
}

function op($operator) {
    $opArgCount = func_num_args() - 1;

    switch ($operator) {
    case '+':
        if ($opArgCount === 0) {
            return function () {
                return foldl1(function ($a, $b) { return $a + $b; }, new ArrayIterator(func_get_args()));
            };
        }
        break;

    default:
        throw new \InvalidArgumentException(sprintf('Invalid operator: %s', $operator));
    }
}
