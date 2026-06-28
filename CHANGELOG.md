# Changelog

All notable changes to this project are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/), and this project adheres to [Semantic Versioning](https://semver.org/).

## [1.0.0] - 2025-02-12

### Added

- Initial release.
- `enumerate()` — entry point to create an enumerable from an iterable or callable.
- `generated()` — entry point to create an enumerable from a generator callable.
- `then()` — chain transformations on an enumerable.
- `into()` — terminal operations (reducers) on an enumerable.
- Transformation functions (`Then.php`): `forEachWith`, `mapBy`, `mapKeysBy`, `filterBy`, `takeWhile`, `dropWhile`, `flatMapBy`, `groupBy`, `windowBy`.
- Terminal functions (`Into.php`): `allOf`, `anyOf`, `minOf`, `minKeyOf`, `maxOf`, `maxKeyOf`, `firstOf`, `firstKeyOf`, `lastOf`, `lastKeyOf`, `stringOf`, `arrayOf`, `done`.
- `EnumerableInterface` — contract for the enumerable type.
- Error handling via configurable `$catching` callable on every function.
- PHPUnit 10.5 test suite with 53+ tests covering normal, empty, and error cases.
- phpDocumentor 3 configuration for API docs generation.
- Comprehensive docblocks with summary lines, parameter/return descriptions, and examples.
