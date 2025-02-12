<?php

namespace Luggsoft\Enumerable\Functions;

use Generator;
use Throwable;
use function Luggsoft\Enumerable\enumerate;

/**
 * @param (callable(mixed,mixed):void) $callable
 * @return (callable(iterable,(callable)):iterable)
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
 * @param (callable(mixed,mixed):mixed) $selector
 * @return (callable(iterable,(callable)):iterable)
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
 * @param (callable(mixed,mixed):mixed) $selector
 * @return (callable(iterable,(callable)):iterable)
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
 * @param (callable(mixed,mixed):bool)|null $predicate
 * @return (callable(iterable,(callable)):iterable)
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
 * @param (callable(mixed,mixed):bool)|null $predicate
 * @return (callable(iterable,(callable)):iterable)
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
 * @param (callable(mixed,mixed):bool)|null $predicate
 * @return (callable(iterable,(callable)):iterable)
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
 * @param (callable(mixed,mixed):mixed)|null $selector
 * @return (callable(iterable,(callable)):iterable)
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
 * @param (callable(mixed,mixed):mixed) $selector
 * @return (callable(iterable,(callable)):iterable)
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

        return enumerate($groups);
    };
}

/**
 * Partitions an iterable into an iterable of equal sized arrays.
 *
 * @param int $size
 * @return (callable(iterable,(callable)):iterable)
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
