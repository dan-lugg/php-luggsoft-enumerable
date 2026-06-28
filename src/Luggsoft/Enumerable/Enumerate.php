<?php

namespace Luggsoft\Enumerable;

use Closure;
use Throwable;
use Traversable;

/**
 * Creates a new enumerable from an iterable or callable.
 *
 * @param  callable|iterable   $iterable An iterable, or a callable that returns an iterable when invoked.
 * @param  callable|null       $catching An optional error handler invoked with Throwable when iteration encounters an error. Defaults to re-throwing.
 * @return EnumerableInterface A new enumerable wrapping the resolved iterable.
 *
 * @example
 * enumerate([1, 2, 3])
 *     ->then(filterBy(fn(int $v): bool => $v > 1))
 *     ->into(arrayOf()); // [1 => 2, 2 => 3]
 *
 * @example
 * enumerate(fn(): array => range(1, 5))
 *     ->then(mapBy(fn(int $v): int => $v ** 2))
 *     ->into(stringOf(', ')); // '1, 4, 9, 16, 25'
 */
function enumerate(callable | iterable $iterable, callable | null $catching = null): EnumerableInterface
{
    return new class (
        iterable: match (true) {
            is_iterable($iterable) => $iterable,
            is_callable($iterable) => call_user_func($iterable),
        },
        catching: $catching ?? fn (Throwable $exception) => throw $exception,
    ) implements EnumerableInterface {
        /**
         * @var iterable The underlying iterable being enumerated.
         */
        private iterable $iterable;

        /**
         * @var Closure The error-handling closure, bound from the $catching callable.
         */
        private Closure $catching;

        /**
         * Constructs the enumerable with the resolved iterable and error handler.
         *
         * @param iterable $iterable The iterable to wrap.
         * @param callable $catching The error handler callable.
         */
        public function __construct(iterable $iterable, callable $catching)
        {
            $this->iterable = $iterable;
            $this->catching = $catching(...);
        }

        /**
         * Applies a transformation to the iterable and returns a new enumerable.
         *
         * @param  callable(iterable, callable):iterable $callable A callable that receives (iterable, callable) and returns the transformed iterable.
         * @return EnumerableInterface                   A new enumerable wrapping the transformed iterable.
         */
        public function then(callable $callable): EnumerableInterface
        {
            return new static(
                iterable: $callable($this->iterable, $this->catching),
                catching: $this->catching
            );
        }

        /**
         * Terminates the enumerable by applying a reducer to the iterable.
         * When no callable is given, materializes the iterable as an array.
         *
         * @param  callable(iterable, callable):mixed|null $callable A terminal callable that receives (iterable, callable) and returns a reduced value, or null to default to iterator_to_array.
         * @return mixed                                   The result of the terminal callable.
         *
         * @example
         * enumerate(['a', 'b', 'c'])->into(); // [0 => 'a', 1 => 'b', 2 => 'c']
         */
        public function into(callable | null $callable = null): mixed
        {
            return ($callable ?? fn ($iterable) => iterator_to_array($iterable))($this->iterable, $this->catching);
        }

        /**
         * Yields each key-value pair from the underlying iterable.
         *
         * @return Traversable A traversable yielding key => value pairs.
         */
        public function getIterator(): Traversable
        {
            foreach ($this->iterable as $key => $value) {
                yield $key => $value;
            }
        }
    };
}
