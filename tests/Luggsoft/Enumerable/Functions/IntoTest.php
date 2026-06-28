<?php

namespace Luggsoft\Enumerable\Functions;

use Exception;

use function Luggsoft\Enumerable\enumerate;

use Luggsoft\Enumerable\TestCaseBase;
use Throwable;

class IntoTest extends TestCaseBase
{
    public function test_into_array_of_empty(): void
    {
        $result = enumerate([])
            ->into(arrayOf());

        $this->assertEquals([], $result);
    }

    public function test_into_array_of_normal(): void
    {
        $enumerable = enumerate([
            enumerate([1, 2, 3]),
            enumerate([4, 5, 6]),
        ]);

        $result = $enumerable->into(arrayOf());
        $this->assertEquals([[1, 2, 3], [4, 5, 6]], $result);
    }

    public function test_into_min_of_empty(): void
    {
        $result = enumerate([])
            ->into(minOf(fn (int $value) => $value));

        $this->assertNull($result);
    }

    public function test_into_min_of_normal_a(): void
    {
        $result = enumerate([1, 2, 3, 4, 5, 6])
            ->into(minOf(fn (int $value) => $value));

        $this->assertEquals(1, $result);
    }

    public function test_into_min_of_normal_b(): void
    {
        $result = enumerate([4, 5, 6, 1, 2, 3])
            ->into(minOf(fn (int $value) => $value));

        $this->assertEquals(1, $result);
    }

    public function test_into_min_of_exception_caught(): void
    {
        $result = enumerate([4, 5, 6, 1, 2, 3], fn (Throwable $e) => null)
            ->into(minOf(fn (int $value) => $value === 2
                ? throw new Exception('Fail')
                : $value));

        $this->assertEquals(1, $result);
    }

    public function test_into_min_of_exception_thrown(): void
    {
        $result = null;

        $this->expectExceptionOfClassIn(Exception::class, function () use (&$result) {
            $result = enumerate([4, 5, 6, 1, 2, 3])
                ->into(minOf(fn (int $value) => $value === 2
                    ? throw new Exception('Fail')
                    : $value));
        });

        $this->assertEquals(null, $result);
    }

    public function test_into_min_key_of_empty(): void
    {
        $result = enumerate([])
            ->into(minKeyOf(fn (int $value) => $value));

        $this->assertNull($result);
    }

    public function test_into_min_key_of_normal_a(): void
    {
        $result = enumerate([1, 2, 3, 4, 5, 6])
            ->into(minKeyOf());

        $this->assertEquals(0, $result);
    }

    public function test_into_min_key_of_normal_b(): void
    {
        $result = enumerate([4, 5, 6, 1, 2, 3])
            ->into(minKeyOf());

        $this->assertEquals(3, $result);
    }

    public function test_into_min_key_of_exception_caught(): void
    {
        $result = enumerate([4, 5, 6, 1, 2, 3], fn (Throwable $e) => null)
            ->into(minKeyOf(fn (int $value) => $value === 2
                ? throw new Exception('Fail')
                : $value));

        $this->assertEquals(3, $result);
    }

    public function test_into_min_key_of_exception_thrown(): void
    {
        $result = null;

        $this->expectExceptionOfClassIn(Exception::class, function () use (&$result) {
            $result = enumerate([4, 5, 6, 1, 2, 3])
                ->into(minKeyOf(fn (int $value) => $value !== 2 ? $value : throw new Exception('Fail')));
        });

        $this->assertEquals(null, $result);
    }

    public function test_into_max_of_empty(): void
    {
        $result = enumerate([])
            ->into(maxOf(fn (int $value) => $value));

        $this->assertNull($result);
    }

    public function test_into_max_of_normal_a(): void
    {
        $result = enumerate([1, 2, 3, 4, 5, 6])
            ->into(maxOf(fn (int $value) => $value));

        $this->assertEquals(6, $result);
    }

    public function test_into_max_of_normal_b(): void
    {
        $result = enumerate([4, 5, 6, 1, 2, 3])
            ->into(maxOf(fn (int $value) => $value));

        $this->assertEquals(6, $result);
    }

    public function test_into_max_of_exception_caught(): void
    {
        $result = enumerate([4, 5, 6, 1, 2, 3], fn (Throwable $e) => null)
            ->into(maxOf(fn (int $value) => $value === 2
                ? throw new Exception('Fail')
                : $value));

        $this->assertEquals(6, $result);
    }

    public function test_into_max_of_exception_thrown(): void
    {
        $result = null;

        $this->expectExceptionOfClassIn(Exception::class, function () use (&$result) {
            $result = enumerate([4, 5, 6, 1, 2, 3])
                ->into(maxOf(fn (int $value) => $value === 2
                    ? throw new Exception('Fail')
                    : $value));
        });

        $this->assertEquals(null, $result);
    }

    public function test_into_done_empty(): void
    {
        $result = enumerate([1, 2, 3])
            ->into(done());

        $this->assertNull($result);
    }

    public function test_into_done_normal(): void
    {
        $result = enumerate([1, 2, 3])
            ->then(mapBy(fn (int $value) => $value % 3))
            ->into(done());

        $this->assertNull($result);
    }

    public function test_into_done_normal_capturing(): void
    {
        $captured = [];

        $result = enumerate([1, 2, 3])
            ->then(forEachWith(function (int $value) use (&$captured) {
                $captured[] = $value;
            }))
            ->into(done());

        $this->assertEquals([1, 2, 3], $captured);
        $this->assertNull($result);
    }

    public function test_into_done_capturing_exception_caught(): void
    {
        $captured = [];

        $result = enumerate([1, 2, 3], fn (Throwable $e) => null)
            ->then(forEachWith(function (int $value) use (&$captured) {
                if ($value === 2) {
                    throw new Exception('Fail');
                }
                $captured[] = $value;
            }))
            ->into(done());

        $this->assertNull($result);
        $this->assertEquals([1, 3], $captured);
    }

    public function test_into_done_capturing_exception_thrown(): void
    {
        $result = null;
        $captured = [];

        $this->expectExceptionOfClassIn(Exception::class, function () use (&$result, &$captured) {
            $result = enumerate([1, 2, 3])
                ->then(forEachWith(function (int $value) use (&$captured) {
                    if ($value === 2) {
                        throw new Exception('Fail');
                    }

                    $captured[] = $value;
                }))
                ->into(done());
        });

        $this->assertEquals(null, $result);
        $this->assertEquals([1], $captured);
    }

    public function test_into_all_of_empty(): void
    {
        $result = enumerate([])
            ->into(allOf());

        $this->assertTrue($result);
    }

    public function test_into_all_of_normal_true(): void
    {
        $result = enumerate([2, 4, 6])
            ->into(allOf(fn (int $v): bool => $v % 2 === 0));

        $this->assertTrue($result);
    }

    public function test_into_all_of_normal_false(): void
    {
        $result = enumerate([2, 3, 4])
            ->into(allOf(fn (int $v): bool => $v % 2 === 0));

        $this->assertFalse($result);
    }

    public function test_into_all_of_exception_caught(): void
    {
        $result = enumerate([2, 4, 6], fn (Throwable $e) => null)
            ->into(allOf(fn (int $v): int => $v === 4
                ? throw new Exception('Fail')
                : $v % 2 === 0));

        $this->assertTrue($result);
    }

    public function test_into_all_of_exception_thrown(): void
    {
        $result = null;

        $this->expectExceptionOfClassIn(Exception::class, function () use (&$result) {
            $result = enumerate([2, 4, 6])
                ->into(allOf(fn (int $v): int => $v === 4
                    ? throw new Exception('Fail')
                    : $v % 2 === 0));
        });

        $this->assertNull($result);
    }

    public function test_into_any_of_empty(): void
    {
        $result = enumerate([])
            ->into(anyOf());

        $this->assertFalse($result);
    }

    public function test_into_any_of_normal_true(): void
    {
        $result = enumerate([1, 2, 3])
            ->into(anyOf(fn (int $v): bool => $v % 2 === 0));

        $this->assertTrue($result);
    }

    public function test_into_any_of_normal_false(): void
    {
        $result = enumerate([1, 3, 5])
            ->into(anyOf(fn (int $v): bool => $v % 2 === 0));

        $this->assertFalse($result);
    }

    public function test_into_any_of_exception_caught(): void
    {
        $result = enumerate([1, 2, 3, 4], fn (Throwable $e) => null)
            ->into(anyOf(fn (int $v): int => $v === 2
                ? throw new Exception('Fail')
                : $v % 2 === 0));

        $this->assertTrue($result); // element 4 matches
    }

    public function test_into_any_of_exception_thrown(): void
    {
        $result = null;

        $this->expectExceptionOfClassIn(Exception::class, function () use (&$result) {
            $result = enumerate([1, 2, 3])
                ->into(anyOf(fn (int $v): int => $v === 2
                    ? throw new Exception('Fail')
                    : $v % 2 === 0));
        });

        $this->assertNull($result);
    }

    public function test_into_first_of_empty(): void
    {
        $result = enumerate([])
            ->into(firstOf());

        $this->assertNull($result);
    }

    public function test_into_first_of_normal(): void
    {
        $result = enumerate([1, 2, 3])
            ->into(firstOf(fn (int $v): bool => $v > 1));

        $this->assertEquals(2, $result);
    }

    public function test_into_first_of_none_match(): void
    {
        $result = enumerate([1, 2, 3])
            ->into(firstOf(fn (int $v): bool => $v > 10));

        $this->assertNull($result);
    }

    public function test_into_first_of_exception_caught(): void
    {
        $result = enumerate([1, 2, 3], fn (Throwable $e) => null)
            ->into(firstOf(fn (int $v): bool => $v === 1
                ? throw new Exception('Fail')
                : $v > 1));

        $this->assertEquals(2, $result);
    }

    public function test_into_first_of_exception_thrown(): void
    {
        $result = null;

        $this->expectExceptionOfClassIn(Exception::class, function () use (&$result) {
            $result = enumerate([1, 2, 3])
                ->into(firstOf(fn (int $v): bool => $v === 1
                    ? throw new Exception('Fail')
                    : $v > 1));
        });

        $this->assertNull($result);
    }

    public function test_into_first_key_of_empty(): void
    {
        $result = enumerate([])
            ->into(firstKeyOf());

        $this->assertNull($result);
    }

    public function test_into_first_key_of_normal(): void
    {
        $result = enumerate(['a' => 1, 'b' => 2, 'c' => 3])
            ->into(firstKeyOf(fn (int $v): bool => $v > 1));

        $this->assertEquals('b', $result);
    }

    public function test_into_first_key_of_exception_caught(): void
    {
        $result = enumerate(['a' => 1, 'b' => 2, 'c' => 3], fn (Throwable $e) => null)
            ->into(firstKeyOf(fn (int $v): bool => $v === 1
                ? throw new Exception('Fail')
                : $v > 1));

        $this->assertEquals('b', $result);
    }

    public function test_into_first_key_of_exception_thrown(): void
    {
        $result = null;

        $this->expectExceptionOfClassIn(Exception::class, function () use (&$result) {
            $result = enumerate(['a' => 1, 'b' => 2, 'c' => 3])
                ->into(firstKeyOf(fn (int $v): bool => $v === 1
                    ? throw new Exception('Fail')
                    : $v > 1));
        });

        $this->assertNull($result);
    }

    public function test_into_last_of_empty(): void
    {
        $result = enumerate([])
            ->into(lastOf());

        $this->assertNull($result);
    }

    public function test_into_last_of_normal(): void
    {
        $result = enumerate([1, 2, 3])
            ->into(lastOf(fn (int $v): bool => $v > 1));

        $this->assertEquals(3, $result);
    }

    public function test_into_last_of_none_match(): void
    {
        $result = enumerate([1, 2, 3])
            ->into(lastOf(fn (int $v): bool => $v > 10));

        $this->assertNull($result);
    }

    public function test_into_last_of_exception_caught(): void
    {
        $result = enumerate([1, 2, 3, 4], fn (Throwable $e) => null)
            ->into(lastOf(fn (int $v): bool => $v === 3
                ? throw new Exception('Fail')
                : $v > 1));

        $this->assertEquals(4, $result);
    }

    public function test_into_last_of_exception_thrown(): void
    {
        $result = null;

        $this->expectExceptionOfClassIn(Exception::class, function () use (&$result) {
            $result = enumerate([1, 2, 3, 4])
                ->into(lastOf(fn (int $v): bool => $v === 3
                    ? throw new Exception('Fail')
                    : $v > 1));
        });

        $this->assertNull($result);
    }

    public function test_into_last_key_of_empty(): void
    {
        $result = enumerate([])
            ->into(lastKeyOf());

        $this->assertNull($result);
    }

    public function test_into_last_key_of_normal(): void
    {
        $result = enumerate(['a' => 1, 'b' => 2, 'c' => 3])
            ->into(lastKeyOf(fn (int $v): bool => $v > 1));

        $this->assertEquals('c', $result);
    }

    public function test_into_last_key_of_exception_caught(): void
    {
        $result = enumerate(['a' => 1, 'b' => 2, 'c' => 3], fn (Throwable $e) => null)
            ->into(lastKeyOf(fn (int $v): bool => $v === 2
                ? throw new Exception('Fail')
                : $v > 1));

        $this->assertEquals('c', $result);
    }

    public function test_into_last_key_of_exception_thrown(): void
    {
        $result = null;

        $this->expectExceptionOfClassIn(Exception::class, function () use (&$result) {
            $result = enumerate(['a' => 1, 'b' => 2, 'c' => 3])
                ->into(lastKeyOf(fn (int $v): bool => $v === 2
                    ? throw new Exception('Fail')
                    : $v > 1));
        });

        $this->assertNull($result);
    }

    public function test_into_string_of_empty(): void
    {
        $result = enumerate([])
            ->into(stringOf());

        $this->assertEquals('', $result);
    }

    public function test_into_string_of_default_delimiter(): void
    {
        $result = enumerate(['a', 'b', 'c'])
            ->into(stringOf());

        $this->assertEquals('abc', $result);
    }

    public function test_into_string_of_with_delimiter(): void
    {
        $result = enumerate(['a', 'b', 'c'])
            ->into(stringOf(', '));

        $this->assertEquals('a, b, c', $result);
    }

    public function test_into_array_of_default_depth(): void
    {
        $result = enumerate([1, 2, 3])
            ->into(arrayOf());

        $this->assertEquals([0 => 1, 1 => 2, 2 => 3], $result);
    }

    public function test_into_array_of_with_depth(): void
    {
        $inner = enumerate([4, 5, 6]);
        $result = enumerate([1, 2, 3, $inner])
            ->into(arrayOf(1));

        $this->assertEquals([0 => 1, 1 => 2, 2 => 3, 3 => [4, 5, 6]], $result);
    }

    public function test_into_default(): void
    {
        $result = enumerate([1, 2, 3])
            ->into();

        $this->assertEquals([0 => 1, 1 => 2, 2 => 3], $result);
    }
}
