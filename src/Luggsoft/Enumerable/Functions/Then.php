<?php

namespace Luggsoft\Enumerable\Functions;

use Generator;
use Throwable;
use function Luggsoft\Enumerable\enumerate;

/**
 * Invokes a side-effect callable for each element, yielding the original elements unchanged.
 *
 * @param (callable(mixed,mixed):void) $callable A callable invoked with (value, key) for each element. Return value is ignored.
 * @return (callable(iterable,(callable)):iterable) A callable that yields the original key-value pairs after invoking $callable.
 *
 * @example
 * enumerate(['a', 'b', 'c'])
 *     ->then(forEachWith(fn(string $v, int $k) => print("$k: $v\n")))
 *     ->into(arrayOf()); // still yields ['a', 'b', 'c']
 */
function forEachWith(callable $callable): callable
{
    return static function (iterable $iterable, callable $catching) use ($callable) {
        foreach ($iterable as $key => $value) {
            try {
                $callable($value, $key);
                yield $key => $value;
            }
            catch (Throwable $exception) {
                $catching($exception);
            }
        }
    };
}


/**
 * Transforms each element using a selector, preserving keys.
 *
 * @param (callable(mixed,mixed):mixed) $selector A callable receiving (value, key) and returning the transformed value.
 * @return (callable(iterable,(callable)):iterable) A callable that yields the transformed values with original keys preserved.
 *
 * @example
 * enumerate([1, 2, 3])
 *     ->then(mapBy(fn(int $v): int => $v * 10))
 *     ->into(arrayOf()); // [0 => 10, 1 => 20, 2 => 30]
 */
function mapBy(callable $selector): callable
{
    return static function (iterable $iterable, callable $catching) use ($selector): iterable {
        foreach ($iterable as $key => $value) {
            try {
                yield $key => $selector($value, $key);
            }
            catch (Throwable $exception) {
                $catching($exception);
            }
        }
    };
}

/**
 * Transforms each key using a selector, preserving values.
 *
 * @param (callable(mixed,mixed):mixed) $selector A callable receiving (value, key) and returning the new key.
 * @return (callable(iterable,(callable)):iterable) A callable that yields values with transformed keys.
 *
 * @example
 * enumerate(['a' => 1, 'b' => 2])
 *     ->then(mapKeysBy(fn(int $v, string $k): string => strtoupper($k)))
 *     ->into(arrayOf()); // ['A' => 1, 'B' => 2]
 */
function mapKeysBy(callable $selector): callable
{
    return static function (iterable $iterable, callable $catching) use ($selector): iterable {
        foreach ($iterable as $key => $value) {
            try {
                yield $selector($value, $key) => $value;
            }
            catch (Throwable $exception) {
                $catching($exception);
            }
        }
    };
}

/**
 * Yields only elements satisfying the predicate.
 *
 * @param (callable(mixed,mixed):bool)|null $predicate A predicate receiving (value, key). Defaults to a truthiness check via empty().
 * @return (callable(iterable,(callable)):iterable) A callable that yields only the key-value pairs for which the predicate returns true.
 *
 * @example
 * enumerate([1, 2, 3, 4])
 *     ->then(filterBy(fn(int $v): bool => $v % 2 === 0))
 *     ->into(arrayOf()); // [1 => 2, 3 => 4]
 */
function filterBy(callable|null $predicate = null): callable
{
    $predicate ??= static fn($value, $key): bool => !empty($value);

    return static function (iterable $iterable, callable $catching) use ($predicate): iterable {
        foreach ($iterable as $key => $value) {
            try {
                if ($predicate($value, $key)) {
                    yield $key => $value;
                }
            }
            catch (Throwable $exception) {
                $catching($exception);
            }
        }
    };
}

/**
 * Yields elements while the predicate holds, then stops iteration.
 *
 * @param (callable(mixed,mixed):bool)|null $predicate A predicate receiving (value, key). Defaults to a truthiness check via empty().
 * @return (callable(iterable,(callable)):iterable) A callable that yields elements until the predicate returns false, then stops.
 *
 * @example
 * enumerate([2, 4, 5, 6])
 *     ->then(takeWhile(fn(int $v): bool => $v % 2 === 0))
 *     ->into(arrayOf()); // [0 => 2, 1 => 4]
 */
function takeWhile(callable|null $predicate = null): callable
{
    $predicate ??= static fn($value, $key): bool => !empty($value);

    return static function (iterable $iterable, callable $catching) use ($predicate): iterable {
        foreach ($iterable as $key => $value) {
            try {
                if ($predicate($value, $key)) {
                    yield $key => $value;
                    continue;
                }

                return;
            }
            catch (Throwable $exception) {
                $catching($exception);
            }
        }
    };
}

/**
 * Skips elements while the predicate holds, then yields the rest.
 *
 * @param (callable(mixed,mixed):bool)|null $predicate A predicate receiving (value, key). Defaults to a truthiness check via empty().
 * @return (callable(iterable,(callable)):iterable) A callable that drops elements while the predicate is true, then yields all remaining elements.
 *
 * @example
 * enumerate([1, 2, 3, 4, 5])
 *     ->then(dropWhile(fn(int $v): bool => $v < 3))
 *     ->into(arrayOf()); // [2 => 3, 3 => 4, 4 => 5]
 */
function dropWhile(callable|null $predicate = null): callable
{
    $predicate ??= static fn($value, $key): bool => !empty($value);

    return static function (iterable $iterable, callable $catching) use ($predicate): iterable {
        $isDrop = true;

        foreach ($iterable as $key => $value) {
            try {
                if ($isDrop && $predicate($value, $key)) {
                    continue;
                }

                $isDrop = false;
                yield $key => $value;
            }
            catch (Throwable $exception) {
                $catching($exception);
            }
        }
    };
}

/**
 * Flattens nested iterables and transforms each element via an optional selector.
 *
 * @param (callable(mixed,mixed):mixed)|null $selector A selector receiving (value, key). Defaults to identity (returns $value).
 * @return (callable(iterable,(callable)):iterable) A callable that recursively flattens nested iterables and yields transformed values.
 *
 * @example
 * enumerate([1, [2, 3], 4])
 *     ->then(flatMapBy())
 *     ->into(arrayOf()); // [0 => 1, 1 => 2, 2 => 3, 3 => 4] (numeric keys)
 */
function flatMapBy(callable|null $selector = null): callable
{
    $selector ??= static fn($value, $key): mixed => $value;

    $recurse = static function (iterable $iterable) use (&$recurse): Generator {
        foreach ($iterable as $key => $value) {
            if (is_iterable($value)) {
                yield from $recurse($value, $key);
                continue;
            }

            yield $value;
        }
    };

    return static function (iterable $iterable, callable $catching) use ($selector, $recurse): iterable {
        foreach ($recurse($iterable) as $key => $value) {
            try {
                yield $selector($value, $key);
            }
            catch (Throwable $exception) {
                $catching($exception);
            }
        }
    };
}

/**
 * Groups elements by a key selected from each element, yielding groups as nested enumerables.
 *
 * @param (callable(mixed,mixed):mixed) $selector A callable receiving (value, key) and returning the group key.
 * @return (callable(iterable,(callable)):iterable) A callable that yields the group key => group elements as an array.
 *
 * @example
 * enumerate(range(1, 6))
 *     ->then(groupBy(fn(int $v): string => $v % 2 === 0 ? 'even' : 'odd'))
 *     ->into(arrayOf()); // ['odd' => [0 => 1, 2 => 3, 4 => 5], 'even' => [1 => 2, 3 => 4, 5 => 6]]
 */
function groupBy(callable $selector): callable
{
    return static function (iterable $iterable, callable $catching) use ($selector): iterable {
        $groups = [];

        foreach ($iterable as $key => $value) {
            try {
                $groups[$selector($value, $key)][$key] = $value;
            }
            catch (Throwable $exception) {
                $catching($exception);
            }
        }

        yield from enumerate($groups);
    };
}

/**
 * Partitions an iterable into an iterable of equal sized arrays.
 *
 * @param int $size The number of elements per window. Must be positive.
 * @return (callable(iterable,(callable)):iterable) A callable that yields arrays of up to $size elements. The final window may be smaller.
 *
 * @example
 * enumerate([1, 2, 3, 4, 5])
 *     ->then(windowBy(2))
 *     ->into(arrayOf()); // [[0 => 1, 1 => 2], [2 => 3, 3 => 4], [4 => 5]]
 */
function windowBy(int $size): callable
{
    return static function (iterable $iterable, callable $catching) use ($size): iterable {
        $window = [];

        foreach ($iterable as $key => $value) {
            try {
                if (count($window) === $size) {
                    yield $window;
                    $window = [];
                }

                $window[$key] = $value;
            }
            catch (Throwable $exception) {
                $catching($exception);
            }
        }

        if (count($window) > 0) {
            yield $window;
        }
    };
}
