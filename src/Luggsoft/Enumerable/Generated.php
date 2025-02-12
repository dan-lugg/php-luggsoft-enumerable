<?php

namespace Luggsoft\Enumerable;

use Closure;
use IteratorAggregate;
use Traversable;

/**
 * @param callable $generator
 * @param callable|null $catching
 * @return EnumerableInterface
 */
function generated(callable $generator, callable|null $catching = null): EnumerableInterface
{
    return enumerate(
        iterable: new class($generator) implements IteratorAggregate {
            /**
             * @var Closure
             */
            private Closure $generator;

            /**
             * @param callable $generator
             */
            public function __construct(callable $generator)
            {
                $this->generator = $generator(...);
            }

            /**
             * @return Traversable
             */
            public function getIterator(): Traversable
            {
                return call_user_func($this->generator);
            }
        },
        catching: $catching,
    );
}