<?php

namespace Luggsoft\Enumerable;

use Closure;
use Throwable;
use Traversable;

/**
 * @param callable|iterable $iterable
 * @param callable|null $catching
 * @return EnumerableInterface
 */
function enumerate(callable|iterable $iterable, callable|null $catching = null): EnumerableInterface
{
    return new class(
        iterable: match (true) {
            is_iterable($iterable) => $iterable,
            is_callable($iterable) => call_user_func($iterable),
        },
        catching: $catching ?? fn(Throwable $exception) => throw $exception,
    ) implements EnumerableInterface {
        /**
         * @var iterable
         */
        private iterable $iterable;

        /**
         * @var Closure
         */
        private Closure $catching;

        /**
         * @param iterable $iterable
         * @param callable $catching
         */
        public function __construct(iterable $iterable, callable $catching)
        {
            $this->iterable = $iterable;
            $this->catching = $catching(...);
        }

        /**
         * @param callable(iterable):iterable $callable
         */
        public function then(callable $callable): EnumerableInterface
        {
            return new static(
                iterable: $callable($this->iterable, $this->catching),
                catching: $this->catching
            );
        }

        /**
         * @param callable(iterable):mixed|null $callable
         * @return mixed
         */
        public function into(callable|null $callable = null): mixed
        {
            return ($callable ?? fn($iterable) => iterator_to_array($iterable))($this->iterable, $this->catching);
        }

        /**
         * @return Traversable
         */
        public function getIterator(): Traversable
        {
            foreach ($this->iterable as $key => $value) {
                yield $key => $value;
            }
        }
    };
}

