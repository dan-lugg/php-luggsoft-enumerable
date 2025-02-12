<?php

namespace Luggsoft\Enumerable;

use IteratorAggregate;

interface EnumerableInterface extends IteratorAggregate
{
    /**
     * @param callable(iterable):iterable $callable
     * @return $this
     */
    public function then(callable $callable): EnumerableInterface;

    /**
     * @param callable(iterable):mixed|null $callable
     * @return mixed
     */
    public function into(callable|null $callable = null): mixed;
}
