<?php

namespace Luggsoft\Enumerable;

use Closure;
use IteratorAggregate;
use Traversable;

/**
 * Creates a new enumerable from a generator callable.
 * The generator callable should return a Traversable (e.g. a Generator from a yield-based function).
 *
 * @param  callable            $generator A callable that returns a Traversable when invoked.
 * @param  callable|null       $catching  An optional error handler invoked with Throwable when iteration encounters an error. Defaults to re-throwing.
 * @return EnumerableInterface A new enumerable wrapping the generated iterable.
 *
 * @example
 * generated(fn(): Generator => yield from [1, 2, 3])
 *     ->into(arrayOf()); // [0 => 1, 1 => 2, 2 => 3]
 */
function generated(callable $generator, callable | null $catching = null): EnumerableInterface
{
    return enumerate(
        iterable: new class ($generator) implements IteratorAggregate {
            /**
             * @var Closure The generator closure, bound from the $generator callable.
             */
            private Closure $generator;

            /**
             * Stores the generator callable for lazy invocation during iteration.
             *
             * @param callable $generator A callable that produces a Traversable.
             */
            public function __construct(callable $generator)
            {
                $this->generator = $generator(...);
            }

            /**
             * Returns the Traversable produced by invoking the stored generator.
             *
             * @return Traversable The traversable produced by the generator.
             */
            public function getIterator(): Traversable
            {
                return call_user_func($this->generator);
            }
        },
        catching: $catching,
    );
}
