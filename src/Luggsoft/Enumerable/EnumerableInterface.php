<?php

namespace Luggsoft\Enumerable;

use IteratorAggregate;

interface EnumerableInterface extends IteratorAggregate
{
    /**
     * Applies a transformation to the enumerable and returns a new enumerable instance.
     *
     * @param  callable(iterable):iterable $callable A callable that receives the current iterable and returns a new iterable.
     * @return $this                       A new enumerable wrapping the transformed iterable.
     *
     * @example
     * enumerate([1, 2, 3])
     *     ->then(fn(iterable $i): iterable => mapBy(fn(int $v): int => $v * 2)($i, fn($e) => throw $e))
     *     ->into(arrayOf());
     */
    public function then(callable $callable): EnumerableInterface;

    /**
     * Terminates the enumerable, reducing it to a single value via the given callable.
     * When no callable is given, the iterable is materialized as an array.
     *
     * @param  callable(iterable):mixed|null $callable A terminal (reducer) callable, or null to default to array conversion.
     * @return mixed                         The reduced value produced by the terminal callable.
     *
     * @example
     * enumerate([1, 2, 3])->into(arrayOf()); // [0 => 1, 1 => 2, 2 => 3]
     * enumerate([1, 2, 3])->into(stringOf(', ')); // '1, 2, 3'
     */
    public function into(callable | null $callable = null): mixed;
}
