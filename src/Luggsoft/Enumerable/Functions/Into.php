<?php

namespace Luggsoft\Enumerable\Functions;

use Throwable;

/**
 * @param (callable(mixed,mixed):bool)|null $predicate
 * @return (callable(iterable,(callable)):bool)
 */
function allOf(callable|null $predicate = null): callable
{
    $predicate ??= static fn($value, $key): bool => !empty($value);

    return static function (iterable $iterable, callable $catching) use ($predicate): bool {
        foreach ($iterable as $key => $value) {
            try {
                if ($predicate($value, $key)) {
                    continue;
                }

                return false;
            }
            catch (Throwable $exception) {
                $catching($exception);
            }
        }

        return true;
    };
}

/**
 * @param (callable(mixed,mixed):bool)|null $predicate
 * @return (callable(iterable,(callable)):bool)
 */
function anyOf(callable|null $predicate = null): callable
{
    $predicate ??= static fn($value, $key): bool => !empty($value);

    return static function (iterable $iterable, callable $catching) use ($predicate): bool {
        foreach ($iterable as $key => $value) {
            try {
                if ($predicate($value, $key)) {
                    return true;
                }
            }
            catch (Throwable $exception) {
                $catching($exception);
            }
        }

        return false;
    };
}

/**
 * @param (callable(mixed,mixed):mixed)|null $selector
 * @return (callable(iterable,(callable)):mixed)
 */
function minOf(callable|null $selector = null): callable
{
    $selector ??= static fn($value, $key): mixed => $value;

    return static function (iterable $iterable, callable $catching) use ($selector): mixed {
        $minValue = null;

        foreach ($iterable as $key => $value) {
            try {
                $nextValue = $selector($value, $key);

                if ($minValue === null || $minValue > $nextValue) {
                    $minValue = $nextValue;
                }
            }
            catch (Throwable $exception) {
                $catching($exception);
            }
        }

        return $minValue;
    };
}

/**
 * @param (callable(mixed,mixed):mixed)|null $selector
 * @return (callable(iterable,(callable)):(int|string|null))
 */
function minKeyOf(callable|null $selector = null): callable
{
    $selector ??= static fn($value, $key): mixed => $value;

    return static function (iterable $iterable, callable $catching) use ($selector): string|int|null {
        $minKey = null;
        $minValue = null;

        foreach ($iterable as $key => $value) {
            try {
                $nextValue = $selector($value, $key);

                if ($minValue === null || $minValue > $nextValue) {
                    $minValue = $nextValue;
                    $minKey = $key;
                }
            }
            catch (Throwable $exception) {
                $catching($exception);
            }
        }

        return $minKey;
    };
}

/**
 * @param (callable(mixed,mixed):mixed)|null $selector
 * @return (callable(iterable,(callable)):mixed)
 */
function maxOf(callable|null $selector = null): callable
{
    $selector ??= static fn($value, $key): mixed => $value;

    return static function (iterable $iterable, callable $catching) use ($selector): mixed {
        $maxValue = null;

        foreach ($iterable as $key => $value) {
            try {
                $nextValue = $selector($value, $key);

                if ($maxValue === null || $maxValue < $nextValue) {
                    $maxValue = $nextValue;

                }
            }
            catch (Throwable $exception) {
                $catching($exception);
            }
        }

        return $maxValue;
    };
}

/**
 * @param (callable(mixed,mixed):mixed)|null $selector
 * @return (callable(iterable,(callable)):(string|int|null))
 */
function maxKeyOf(callable|null $selector = null): callable
{
    $selector ??= static fn($value, $key): mixed => $value;

    return static function (iterable $iterable, callable $catching) use ($selector): string|int|null {
        $maxKey = null;
        $maxValue = null;

        foreach ($iterable as $key => $value) {
            try {
                $nextValue = $selector($value, $key);

                if ($maxValue === null || $maxValue < $nextValue) {
                    $maxKey = $key;

                }
            }
            catch (Throwable $exception) {
                $catching($exception);
            }
        }

        return $maxKey;
    };
}

/**
 * @param (callable(mixed,mixed):mixed)|null $predicate
 * @return (callable(iterable,(callable)):mixed)
 */
function firstOf(callable|null $predicate = null): callable
{
    $predicate ??= static fn($value, $key): bool => !empty($value);

    return static function (iterable $iterable, callable $catching) use ($predicate): mixed {
        foreach ($iterable as $key => $value) {
            try {
                if ($predicate($value, $key)) {
                    return $value;
                }
            }
            catch (Throwable $exception) {
                $catching($exception);
            }
        }

        return null;
    };
}

/**
 * @param (callable(mixed,mixed):bool)|null $predicate
 * @return (callable(iterable,(callable)):(string|int|null))
 */
function firstKeyOf(callable|null $predicate = null): callable
{
    $predicate ??= static fn($value, $key): bool => !empty($value);

    return static function (iterable $iterable, callable $catching) use ($predicate): string|int|null {
        foreach ($iterable as $key => $value) {
            try {
                if ($predicate($value, $key)) {
                    return $key;
                }
            }
            catch (Throwable $exception) {
                $catching($exception);
            }
        }

        return null;
    };
}

/**
 * @param (callable(mixed,mixed):bool)|null $predicate
 * @return (callable(iterable,(callable)):mixed)
 */
function lastOf(callable|null $predicate = null): callable
{
    $predicate ??= static fn($value, $key): bool => !empty($value);

    return static function (iterable $iterable, callable $catching) use ($predicate): mixed {
        $lastValue = null;
        foreach ($iterable as $key => $value) {
            try {
                if ($predicate($value, $key)) {
                    $lastValue = $value;
                }
            }
            catch (Throwable $exception) {
                $catching($exception);
            }
        }

        return $lastValue;
    };
}

/**
 * @param (callable(mixed,mixed):bool)|null $predicate
 * @return (callable(iterable,(callable)):(string|int|null))
 */
function lastKeyOf(callable|null $predicate = null): callable
{
    $predicate ??= static fn($value, $key): bool => !empty($value);

    return static function (iterable $iterable, callable $catching) use ($predicate): string|int|null {
        $lastKey = null;

        foreach ($iterable as $key => $value) {
            try {
                if ($predicate($value, $key)) {
                    $lastKey = $key;
                }
            }
            catch (Throwable $exception) {
                $catching($exception);
            }
        }

        return $lastKey;
    };
}

/**
 * @param string $delimiter
 * @return callable(iterable):string
 */
function stringOf(string $delimiter = ""): callable
{
    return static fn(iterable $iterable): string => implode($delimiter, iterator_to_array($iterable));
}

/**
 * Returns a function that
 *
 * @param int $depth
 * @return (callable(iterable):array)
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

    return static fn(iterable $iterable): array => $recurse($iterable, $depth);
}

/**
 * @return (callable(iterable):void)
 */
function done(): callable
{
    return static function (iterable $iterable): void {
        /** @noinspection PhpStatementHasEmptyBodyInspection */
        foreach ($iterable as $ignored) ;
    };
}