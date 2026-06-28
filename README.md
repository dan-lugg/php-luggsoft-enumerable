# luggsoft/enumerable

[![Packagist Version](https://img.shields.io/packagist/v/luggsoft/enumerable)](https://packagist.org/packages/luggsoft/enumerable)
[![PHP Version](https://img.shields.io/packagist/php-v/luggsoft/enumerable)](https://packagist.org/packages/luggsoft/enumerable)
[![License](https://img.shields.io/github/license/luggsoft/luggsoft-php-enumerable)](LICENSE)
[![CI](https://github.com/luggsoft/luggsoft-php-enumerable/actions/workflows/main.yml/badge.svg)](https://github.com/luggsoft/luggsoft-php-enumerable/actions/workflows/main.yml)

A fluent, lazy enumeration library for PHP inspired by LINQ and Java Streams. Provides a unified API for working with
arrays, iterators, and generators through composable transformations and terminal operations.

## Installation

```bash
composer require luggsoft/enumerable
```

Requires PHP 8.1+.

## Quick Start

```php
use function Luggsoft\Enumerable\enumerate;

$result = enumerate([1, 2, 3, 4, 5, 6])
    ->then(filterBy(fn(int $v): bool => $v % 2 === 0))
    ->then(mapBy(fn(int $v): int => $v * 10))
    ->into(arrayOf());

// [1 => 20, 3 => 40, 5 => 60]
```

## How It Works

Every enumerable pipeline has three parts:

1. **Entry point** — `enumerate()` or `generated()` creates an enumerable from an array, iterator, generator, or callable.
2. **Transformations** (`->then(...)`) — apply lazy operations like mapping, filtering, grouping, and windowing.
3. **Terminal** (`->into(...)`) — consumes the pipeline and produces a value (array, string, bool, or custom).

Operations are **lazy** — no iteration happens until `->into()` is called. The `$catching` parameter on every function
provides error handling for individual elements without stopping the entire iteration.

## Entry Points

| Function | Description |
|----------|-------------|
| `enumerate(iterable\|callable, catching?)` | Creates an enumerable from an iterable or callable |
| `generated(callable, catching?)` | Creates an enumerable from a generator callable |

## Transformations (`->then(...)`)

| Function | Description |
|----------|-------------|
| `forEachWith(callable)` | Side-effect callable for each element; yields originals unchanged |
| `mapBy(callable)` | Transforms each element (preserves keys) |
| `mapKeysBy(callable)` | Transforms each key (preserves values) |
| `filterBy(predicate?)` | Yields only elements matching the predicate |
| `takeWhile(predicate?)` | Yields elements while the predicate holds, then stops |
| `dropWhile(predicate?)` | Skips elements while the predicate holds, then yields the rest |
| `flatMapBy(selector?)` | Recursively flattens nested iterables with optional transform |
| `groupBy(callable)` | Groups elements by a selected key |
| `windowBy(int)` | Partitions into equal-sized window arrays |

## Terminals (`->into(...)`)

| Function | Returns | Description |
|----------|---------|-------------|
| `allOf(predicate?)` | `bool` | True if all elements satisfy the predicate |
| `anyOf(predicate?)` | `bool` | True if any element satisfies the predicate |
| `minOf(selector?)` | `mixed` | Minimum selected value |
| `minKeyOf(selector?)` | `int\|string\|null` | Key of the minimum selected value |
| `maxOf(selector?)` | `mixed` | Maximum selected value |
| `maxKeyOf(selector?)` | `int\|string\|null` | Key of the maximum selected value |
| `firstOf(predicate?)` | `mixed` | First element matching the predicate |
| `firstKeyOf(predicate?)` | `int\|string\|null` | First key matching the predicate |
| `lastOf(predicate?)` | `mixed` | Last element matching the predicate |
| `lastKeyOf(predicate?)` | `int\|string\|null` | Last key matching the predicate |
| `stringOf(delimiter?)` | `string` | Elements joined by delimiter |
| `arrayOf(depth?)` | `array` | Materializes to an array (with optional recursive depth) |
| `done()` | `void` | Consumes the enumerable (use for side effects only) |

## Error Handling

Every function accepts a `$catching` callable that handles exceptions thrown during iteration:

```php
use function Luggsoft\Enumerable\enumerate;

$result = enumerate([1, 2, 3, 4, 5], fn(Throwable $e) => null)
    ->then(mapBy(fn(int $v): int => match ($v) {
        3 => throw new \Exception("skip"),
        default => $v * 10,
    }))
    ->into(arrayOf());

// [0 => 10, 1 => 20, 3 => 40, 4 => 50] — element 2 skipped, error caught
```

Without a `$catching` handler, exceptions propagate normally and stop iteration.

## Examples

```php
// Chaining multiple transformations
$result = enumerate(['foo', 'bar', 'baz', 'qux'])
    ->then(filterBy(fn(string $v): bool => strlen($v) === 3))
    ->then(mapKeysBy(fn(string $v, int $k): string => strtoupper($v)))
    ->into(arrayOf());
// ['FOO' => 'foo', 'BAR' => 'bar', 'BAZ' => 'baz', 'QUX' => 'qux']

// Grouping
$result = enumerate(range(1, 10))
    ->then(groupBy(fn(int $v): string => $v % 2 === 0 ? 'even' : 'odd'))
    ->into(arrayOf());
// ['odd' => [0 => 1, 2 => 3, ...], 'even' => [1 => 2, 3 => 4, ...]]

// Windowing
$result = enumerate([1, 2, 3, 4, 5])
    ->then(windowBy(2))
    ->into(arrayOf());
// [[0 => 1, 1 => 2], [2 => 3, 3 => 4], [4 => 5]]
```

## Development

```bash
composer install          # Install dependencies
composer test             # Run tests
composer analyse          # Static analysis (PHPStan)
composer cs               # Check code style
composer cs-fix           # Fix code style automatically
composer docs             # Generate API documentation
```

## License

`luggsoft/enumerable` is open-sourced software licensed under the [MIT license](LICENSE).
