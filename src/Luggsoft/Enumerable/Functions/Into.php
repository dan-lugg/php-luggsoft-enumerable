<?php

declare(strict_types=1);

namespace Luggsoft\Enumerable\Functions;

use Throwable;

/**
 * Returns true if all elements satisfy the predicate.
 *
 * @param  (callable(mixed,mixed):bool)|null    $predicate A predicate receiving (value, key). Defaults to a truthiness check via empty().
 * @return (callable(iterable,(callable)):bool) A callable that consumes an iterable and returns true if every element passes the predicate.
 *
 * @example
 * enumerate([2, 4, 6])->into(allOf(fn(int $v): bool => $v % 2 === 0)); // true
 * enumerate([2, 3, 4])->into(allOf(fn(int $v): bool => $v % 2 === 0)); // false
 */
function allOf(callable | null $predicate = null): callable
{
    $predicate ??= static fn ($value, $key): bool => !empty($value);

    return static function (iterable $iterable, callable $catching) use ($predicate): bool {
        foreach ($iterable as $key => $value) {
            try {
                if ($predicate($value, $key)) {
                    continue;
                }

                return false;
            } catch (Throwable $exception) {
                $catching($exception);
            }
        }

        return true;
    };
}

/**
 * Returns true if any element satisfies the predicate.
 *
 * @param  (callable(mixed,mixed):bool)|null    $predicate A predicate receiving (value, key). Defaults to a truthiness check via empty().
 * @return (callable(iterable,(callable)):bool) A callable that consumes an iterable and returns true if at least one element passes the predicate.
 *
 * @example
 * enumerate([1, 3, 5])->into(anyOf(fn(int $v): bool => $v % 2 === 0)); // false
 * enumerate([1, 2, 3])->into(anyOf(fn(int $v): bool => $v % 2 === 0)); // true
 */
function anyOf(callable | null $predicate = null): callable
{
    $predicate ??= static fn ($value, $key): bool => !empty($value);

    return static function (iterable $iterable, callable $catching) use ($predicate): bool {
        foreach ($iterable as $key => $value) {
            try {
                if ($predicate($value, $key)) {
                    return true;
                }
            } catch (Throwable $exception) {
                $catching($exception);
            }
        }

        return false;
    };
}

/**
 * Returns the minimum selected value from the iterable.
 *
 * @param  (callable(mixed,mixed):mixed)|null    $selector A selector receiving (value, key). Defaults to identity (returns $value).
 * @return (callable(iterable,(callable)):mixed) A callable that consumes an iterable and returns the minimum selected value, or null if empty.
 *
 * @example
 * enumerate([3, 1, 4, 1, 5])->into(minOf()); // 1
 * enumerate(['cat', 'dog', 'elephant'])->into(minOf(fn(string $v): int => strlen($v))); // 'cat' (length 3)
 */
function minOf(callable | null $selector = null): callable
{
    $selector ??= static fn ($value, $key): mixed => $value;

    return static function (iterable $iterable, callable $catching) use ($selector): mixed {
        $minValue = null;

        foreach ($iterable as $key => $value) {
            try {
                $nextValue = $selector($value, $key);

                if ($minValue === null || $minValue > $nextValue) {
                    $minValue = $nextValue;
                }
            } catch (Throwable $exception) {
                $catching($exception);
            }
        }

        return $minValue;
    };
}

/**
 * Returns the key of the minimum selected value.
 *
 * @param  (callable(mixed,mixed):mixed)|null                $selector A selector receiving (value, key). Defaults to identity (returns $value).
 * @return (callable(iterable,(callable)):(int|string|null)) A callable that consumes an iterable and returns the key of the minimum selected value, or null if empty.
 *
 * @example
 * enumerate(['x' => 10, 'y' => 3, 'z' => 7])->into(minKeyOf()); // 'y'
 */
function minKeyOf(callable | null $selector = null): callable
{
    $selector ??= static fn ($value, $key): mixed => $value;

    return static function (iterable $iterable, callable $catching) use ($selector): string | int | null {
        $minKey = null;
        $minValue = null;

        foreach ($iterable as $key => $value) {
            try {
                $nextValue = $selector($value, $key);

                if ($minValue === null || $minValue > $nextValue) {
                    $minValue = $nextValue;
                    $minKey = $key;
                }
            } catch (Throwable $exception) {
                $catching($exception);
            }
        }

        return $minKey;
    };
}

/**
 * Returns the maximum selected value from the iterable.
 *
 * @param  (callable(mixed,mixed):mixed)|null    $selector A selector receiving (value, key). Defaults to identity (returns $value).
 * @return (callable(iterable,(callable)):mixed) A callable that consumes an iterable and returns the maximum selected value, or null if empty.
 *
 * @example
 * enumerate([3, 1, 4, 1, 5])->into(maxOf()); // 5
 * enumerate(['cat', 'elephant', 'dog'])->into(maxOf(fn(string $v): int => strlen($v))); // 'elephant' (length 8)
 */
function maxOf(callable | null $selector = null): callable
{
    $selector ??= static fn ($value, $key): mixed => $value;

    return static function (iterable $iterable, callable $catching) use ($selector): mixed {
        $maxValue = null;

        foreach ($iterable as $key => $value) {
            try {
                $nextValue = $selector($value, $key);

                if ($maxValue === null || $maxValue < $nextValue) {
                    $maxValue = $nextValue;

                }
            } catch (Throwable $exception) {
                $catching($exception);
            }
        }

        return $maxValue;
    };
}

/**
 * Returns the key of the maximum selected value.
 *
 * @param  (callable(mixed,mixed):mixed)|null                $selector A selector receiving (value, key). Defaults to identity (returns $value).
 * @return (callable(iterable,(callable)):(string|int|null)) A callable that consumes an iterable and returns the key of the maximum selected value, or null if empty.
 *
 * @example
 * enumerate(['x' => 10, 'y' => 3, 'z' => 7])->into(maxKeyOf()); // 'x'
 */
function maxKeyOf(callable | null $selector = null): callable
{
    $selector ??= static fn ($value, $key): mixed => $value;

    return static function (iterable $iterable, callable $catching) use ($selector): string | int | null {
        $maxKey = null;
        $maxValue = null;

        foreach ($iterable as $key => $value) {
            try {
                $nextValue = $selector($value, $key);

                if ($maxValue === null || $maxValue < $nextValue) {
                    $maxValue = $nextValue;
                    $maxKey = $key;
                }
            } catch (Throwable $exception) {
                $catching($exception);
            }
        }

        return $maxKey;
    };
}

/**
 * Returns the first element satisfying the predicate.
 *
 * @param  (callable(mixed,mixed):mixed)|null    $predicate A predicate receiving (value, key). Defaults to a truthiness check via empty().
 * @return (callable(iterable,(callable)):mixed) A callable that consumes an iterable and returns the first matching value, or null if none found.
 *
 * @example
 * enumerate([1, 2, 3])->into(firstOf(fn(int $v): bool => $v > 1)); // 2
 * enumerate([])->into(firstOf()); // null
 */
function firstOf(callable | null $predicate = null): callable
{
    $predicate ??= static fn ($value, $key): bool => !empty($value);

    return static function (iterable $iterable, callable $catching) use ($predicate): mixed {
        foreach ($iterable as $key => $value) {
            try {
                if ($predicate($value, $key)) {
                    return $value;
                }
            } catch (Throwable $exception) {
                $catching($exception);
            }
        }

        return null;
    };
}

/**
 * Returns the first key satisfying the predicate.
 *
 * @param  (callable(mixed,mixed):bool)|null                 $predicate A predicate receiving (value, key). Defaults to a truthiness check via empty().
 * @return (callable(iterable,(callable)):(string|int|null)) A callable that consumes an iterable and returns the first matching key, or null if none found.
 *
 * @example
 * enumerate(['a' => 1, 'b' => 2, 'c' => 3])->into(firstKeyOf(fn(int $v): bool => $v > 1)); // 'b'
 */
function firstKeyOf(callable | null $predicate = null): callable
{
    $predicate ??= static fn ($value, $key): bool => !empty($value);

    return static function (iterable $iterable, callable $catching) use ($predicate): string | int | null {
        foreach ($iterable as $key => $value) {
            try {
                if ($predicate($value, $key)) {
                    return $key;
                }
            } catch (Throwable $exception) {
                $catching($exception);
            }
        }

        return null;
    };
}

/**
 * Returns the last element satisfying the predicate.
 *
 * @param  (callable(mixed,mixed):bool)|null     $predicate A predicate receiving (value, key). Defaults to a truthiness check via empty().
 * @return (callable(iterable,(callable)):mixed) A callable that consumes an iterable and returns the last matching value, or null if none found.
 *
 * @example
 * enumerate([1, 2, 3])->into(lastOf(fn(int $v): bool => $v > 1)); // 3
 */
function lastOf(callable | null $predicate = null): callable
{
    $predicate ??= static fn ($value, $key): bool => !empty($value);

    return static function (iterable $iterable, callable $catching) use ($predicate): mixed {
        $lastValue = null;
        foreach ($iterable as $key => $value) {
            try {
                if ($predicate($value, $key)) {
                    $lastValue = $value;
                }
            } catch (Throwable $exception) {
                $catching($exception);
            }
        }

        return $lastValue;
    };
}

/**
 * Returns the last key satisfying the predicate.
 *
 * @param  (callable(mixed,mixed):bool)|null                 $predicate A predicate receiving (value, key). Defaults to a truthiness check via empty().
 * @return (callable(iterable,(callable)):(string|int|null)) A callable that consumes an iterable and returns the last matching key, or null if none found.
 *
 * @example
 * enumerate(['a' => 1, 'b' => 2, 'c' => 3])->into(lastKeyOf(fn(int $v): bool => $v > 1)); // 'c'
 */
function lastKeyOf(callable | null $predicate = null): callable
{
    $predicate ??= static fn ($value, $key): bool => !empty($value);

    return static function (iterable $iterable, callable $catching) use ($predicate): string | int | null {
        $lastKey = null;

        foreach ($iterable as $key => $value) {
            try {
                if ($predicate($value, $key)) {
                    $lastKey = $key;
                }
            } catch (Throwable $exception) {
                $catching($exception);
            }
        }

        return $lastKey;
    };
}

/**
 * Joins elements into a string with the given delimiter.
 *
 * @param  string                    $delimiter The delimiter inserted between each element. Defaults to an empty string.
 * @return callable(iterable):string A callable that consumes an iterable and returns the joined string.
 *
 * @example
 * enumerate(['a', 'b', 'c'])->into(stringOf(', ')); // 'a, b, c'
 */
function stringOf(string $delimiter = ''): callable
{
    return static fn (iterable $iterable): string => implode($delimiter, iterator_to_array($iterable));
}

/**
 * Converts the iterable to an array with optional recursive depth.
 *
 * @param  int                        $depth The maximum recursion depth for nested iterables. Use -1 for unlimited (default).
 * @return (callable(iterable):array) A callable that consumes an iterable and returns the resulting array.
 *
 * @example
 * enumerate(['a', 'b', 'c'])->into(arrayOf()); // [0 => 'a', 1 => 'b', 2 => 'c']
 * enumerate([['nested' => true]])->into(arrayOf(1)); // [0 => ['nested' => true]]
 */
function arrayOf(int $depth = -1): callable
{
    $recurse = static function (iterable $iterable, int $depth) use (&$recurse): array {
        $array = [];

        foreach ($iterable as $key => $value) {
            if ($depth !== 0 && is_iterable($value)) {
                $array[$key] = $recurse($value, $depth - 1);
                continue;
            }

            $array[$key] = $value;
        }

        return $array;
    };

    return static fn (iterable $iterable): array => $recurse($iterable, $depth);
}

/**
 * Consumes the enumerable without producing a value (void iteration).
 * Useful when you only care about side effects (e.g. forEachWith).
 *
 * @return (callable(iterable):void) A callable that iterates the iterable and discards all elements.
 *
 * @example
 * enumerate([1, 2, 3])
 *     ->then(forEachWith(fn(int $v) => printf("%d\n", $v)))
 *     ->into(done());
 */
function done(): callable
{
    return static function (iterable $iterable): void {
        /** @noinspection PhpStatementHasEmptyBodyInspection */
        foreach ($iterable as $ignored);
    };
}
