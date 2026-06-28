<?php

namespace Luggsoft\Enumerable\Functions;

use Exception;

use function Luggsoft\Enumerable\enumerate;

use Luggsoft\Enumerable\TestCaseBase;
use Throwable;

class ThenTest extends TestCaseBase
{
    public function test_then_map_by_empty(): void
    {
        $result = [];
        $enumerable = enumerate([])
            ->then(mapBy(fn (int $value) => $value * 3));

        foreach ($enumerable as $key => $value) {
            $result[$key] = $value;
        }

        $this->assertEquals([], $result);
    }

    public function test_then_map_by_normal(): void
    {
        $result = [];
        $enumerable = enumerate([0 => 1, 1 => 2, 2 => 3])
            ->then(mapBy(fn (int $value) => $value * 3));

        foreach ($enumerable as $key => $value) {
            $result[$key] = $value;
        }

        $this->assertEquals([0 => 3, 1 => 6, 2 => 9], $result);
    }

    public function test_then_map_by_exception_caught(): void
    {
        $result = [];
        $enumerable = enumerate([0 => 1, 1 => 2, 2 => 3], fn (Throwable $e) => null)
            ->then(mapBy(fn (int $value) => $value === 2
                ? throw new Exception('Fail')
                : $value));

        foreach ($enumerable as $key => $value) {
            $result[$key] = $value;
        }

        $this->assertEquals([0 => 1, 2 => 3], $result);
    }

    public function test_then_map_by_exception_thrown(): void
    {
        $result = [];

        $this->expectExceptionOfClassIn(Exception::class, function () use (&$result) {
            $enumerable = enumerate([0 => 1, 1 => 2, 2 => 3])
                ->then(mapBy(fn (int $value) => $value === 2
                    ? throw new Exception('Fail')
                    : $value));

            foreach ($enumerable as $key => $value) {
                $result[$key] = $value;
            }
        });

        $this->assertEquals([0 => 1], $result);
    }

    public function test_then_map_keys_by_empty(): void
    {
        $result = [];
        $enumerable = enumerate([])
            ->then(mapKeysBy(fn (int $value) => "key_$value"));

        foreach ($enumerable as $key => $value) {
            $result[$key] = $value;
        }

        $this->assertEquals([], $result);
    }

    public function test_then_map_keys_by_normal(): void
    {
        $result = [];
        $enumerable = enumerate([1, 2, 3])
            ->then(mapKeysBy(fn (int $value) => "key_$value"));

        foreach ($enumerable as $key => $value) {
            $result[$key] = $value;
        }

        $this->assertEquals(['key_1' => 1, 'key_2' => 2, 'key_3' => 3], $result);
    }

    public function test_then_map_keys_by_exception_caught(): void
    {
        $result = [];
        $enumerable = enumerate([0 => 1, 1 => 2, 2 => 3], fn (Throwable $e) => null)
            ->then(mapKeysBy(fn (int $value) => ($value === 2
                ? throw new Exception('Fail')
                : "key_$value")));

        foreach ($enumerable as $key => $value) {
            $result[$key] = $value;
        }

        $this->assertEquals(['key_1' => 1, 'key_3' => 3], $result);
    }

    public function test_then_map_keys_by_exception_thrown(): void
    {
        $result = [];

        $this->expectExceptionOfClassIn(Exception::class, function () use (&$result) {
            $enumerable = enumerate([0 => 1, 1 => 2, 2 => 3])
                ->then(mapKeysBy(fn (int $value) => $value === 2
                    ? throw new Exception('Fail')
                    : "key_$value"));

            foreach ($enumerable as $key => $value) {
                $result[$key] = $value;
            }
        });

        $this->assertEquals(['key_1' => 1], $result);
    }

    public function test_then_filter_by_default_predicate(): void
    {
        $result = [];

        $enumerable = enumerate([])
            ->then(filterBy());

        foreach ($enumerable as $key => $value) {
            $result[$key] = $value;
        }

        $this->assertEquals([], $result);
    }

    public function test_then_filter_by_empty(): void
    {
        $result = [];

        $enumerable = enumerate([])
            ->then(filterBy(fn (int $value) => $value * 3));

        foreach ($enumerable as $key => $value) {
            $result[$key] = $value;
        }

        $this->assertEquals([], $result);
    }

    public function test_then_filter_by_normal(): void
    {
        $result = [];

        $enumerable = enumerate([0 => 1, 1 => 2, 2 => 3, 3 => 4])
            ->then(filterBy(fn (int $value) => $value % 2 === 0));

        foreach ($enumerable as $key => $value) {
            $result[$key] = $value;
        }

        $this->assertEquals([1 => 2, 3 => 4], $result);
    }

    public function test_then_filter_by_exception_caught(): void
    {
        $result = [];

        $enumerable = enumerate([0 => 1, 1 => 2, 2 => 3, 3 => 4], fn (Throwable $e) => null)
            ->then(filterBy(fn (int $value) => $value === 2
                ? throw new Exception('Fail')
                : $value % 2 === 0));

        foreach ($enumerable as $key => $value) {
            $result[$key] = $value;
        }

        $this->assertEquals([3 => 4], $result);
    }

    public function test_then_filter_by_exception_thrown(): void
    {
        $result = [];

        $this->expectExceptionOfClassIn(Exception::class, function () use (&$result) {
            $enumerable = enumerate([0 => 1, 1 => 2, 2 => 3])
                ->then(filterBy(fn (int $value) => $value === 2
                    ? throw new Exception('Fail')
                    : ($value % 2 === 0)));

            foreach ($enumerable as $key => $value) {
                $result[$key] = $value;
            }
        });

        $this->assertEquals([], $result);
    }

    public function test_then_take_while_default_predicate(): void
    {
        $result = [];
        $enumerable = enumerate([])
            ->then(takeWhile());

        foreach ($enumerable as $key => $value) {
            $result[$key] = $value;
        }

        $this->assertEquals([], $result);
    }

    public function test_then_take_while_empty(): void
    {
        $result = [];
        $enumerable = enumerate([])
            ->then(takeWhile(fn (int $value) => $value <= 3));

        foreach ($enumerable as $key => $value) {
            $result[$key] = $value;
        }

        $this->assertEquals([], $result);
    }

    public function test_then_take_while_normal(): void
    {
        $result = [];
        $enumerable = enumerate([1, 2, 3, 4, 5, 6])
            ->then(takeWhile(fn (int $value) => $value <= 3));

        foreach ($enumerable as $key => $value) {
            $result[$key] = $value;
        }

        $this->assertEquals([1, 2, 3], $result);
    }

    public function test_then_take_while_exception_caught(): void
    {
        $result = [];
        $enumerable = enumerate([1, 2, 3, 4, 5, 6], fn (Throwable $e) => null)
            ->then(takeWhile(fn (int $value) => $value === 2
                ? throw new Exception('Fail')
                : $value <= 3));

        foreach ($enumerable as $key => $value) {
            $result[$key] = $value;
        }

        $this->assertEquals([0 => 1, 2 => 3], $result);
    }

    public function test_then_take_while_exception_thrown(): void
    {
        $result = [];

        $this->expectExceptionOfClassIn(Exception::class, function () use (&$result) {
            $enumerable = enumerate([0 => 1, 1 => 2, 2 => 3])
                ->then(takeWhile(fn (int $value) => $value === 2
                    ? throw new Exception('Fail')
                    : $value <= 3));

            foreach ($enumerable as $key => $value) {
                $result[$key] = $value;
            }
        });

        $this->assertEquals([0 => 1], $result);
    }

    public function test_then_drop_while_default_predicate(): void
    {
        $result = [];
        $enumerable = enumerate([])
            ->then(dropWhile());

        foreach ($enumerable as $key => $value) {
            $result[$key] = $value;
        }

        $this->assertEquals([], $result);
    }

    public function test_then_drop_while_empty(): void
    {
        $result = [];
        $enumerable = enumerate([])
            ->then(dropWhile(fn (int $value) => $value <= 3));

        foreach ($enumerable as $key => $value) {
            $result[$key] = $value;
        }

        $this->assertEquals([], $result);
    }

    public function test_then_drop_while_normal(): void
    {
        $result = [];
        $enumerable = enumerate([1, 2, 3, 4, 5, 6])
            ->then(dropWhile(fn (int $value) => $value <= 3));

        foreach ($enumerable as $key => $value) {
            $result[$key] = $value;
        }

        $this->assertEquals([3 => 4, 4 => 5, 5 => 6], $result);
    }

    public function test_then_drop_while_exception_caught(): void
    {
        $result = [];
        $enumerable = enumerate([1, 2, 3, 4, 5, 6], fn (Throwable $e) => null)
            ->then(dropWhile(fn (int $value) => $value === 2
                ? throw new Exception('Fail')
                : $value <= 3));

        foreach ($enumerable as $key => $value) {
            $result[$key] = $value;
        }

        $this->assertEquals([3 => 4, 4 => 5, 5 => 6], $result);
    }

    public function test_then_drop_while_exception_thrown(): void
    {
        $result = [];

        $this->expectExceptionOfClassIn(Exception::class, function () use (&$result) {
            $enumerable = enumerate([0 => 1, 1 => 2, 2 => 3])
                ->then(dropWhile(fn (int $value) => $value === 2
                    ? throw new Exception('Fail')
                    : $value <= 3));

            foreach ($enumerable as $key => $value) {
                $result[$key] = $value;
            }
        });

        $this->assertEquals([], $result);
    }

    public function test_then_group_by_empty(): void
    {
        $result = [];
        $enumerable = enumerate([])
            ->then(groupBy(fn (int $value) => $value % 3));

        foreach ($enumerable as $key => $value) {
            $result[$key] = $value;
        }

        $this->assertEquals([], $result);
    }

    public function test_then_group_by_normal(): void
    {
        $result = [];
        $enumerable = enumerate([1, 2, 3, 4, 5, 6, 7, 8, 9])
            ->then(groupBy(fn (int $value) => $value % 3));

        foreach ($enumerable as $key => $value) {
            $result[$key] = $value;
        }

        $this->assertEquals([2 => 3, 5 => 6, 8 => 9], $result[0]);
        $this->assertEquals([0 => 1, 3 => 4, 6 => 7], $result[1]);
        $this->assertEquals([1 => 2, 4 => 5, 7 => 8], $result[2]);
    }

    public function test_then_group_by_exception_caught(): void
    {
        $result = [];
        $enumerable = enumerate([1, 2, 3, 4, 5, 6], fn (Throwable $e) => null)
            ->then(groupBy(fn (int $value) => $value === 2
                ? throw new Exception('Fail')
                : $value % 3));

        foreach ($enumerable as $key => $value) {
            $result[$key] = $value;
        }

        $this->assertEquals([2 => 3, 5 => 6], $result[0]);
        $this->assertEquals([0 => 1, 3 => 4], $result[1]);
        $this->assertEquals([4 => 5], $result[2]);
    }

    public function test_then_group_by_exception_thrown(): void
    {
        $result = [];

        $this->expectExceptionOfClassIn(Exception::class, function () use (&$result) {
            $enumerable = enumerate([0 => 1, 1 => 2, 2 => 3])
                ->then(groupBy(fn (int $value) => $value === 2
                    ? throw new Exception('Fail')
                    : $value % 3));

            foreach ($enumerable as $key => $value) {
                $result[$key] = $value;
            }
        });

        $this->assertEquals([], $result);
    }

    public function test_then_window_by_empty(): void
    {
        $result = [];
        $enumerable = enumerate([])
            ->then(windowBy(3));

        foreach ($enumerable as $key => $value) {
            $result[$key] = $value;
        }

        $this->assertEquals([], $result);
    }

    public function test_then_window_by_normal_size3(): void
    {
        $result = [];
        $enumerable = enumerate([1, 2, 3, 4, 5, 6, 7, 8, 9])
            ->then(windowBy(3));

        foreach ($enumerable as $key => $value) {
            $result[$key] = $value;
        }

        $this->assertCount(3, $result);
        $this->assertEquals([0 => 1, 1 => 2, 2 => 3], $result[0]);
        $this->assertEquals([3 => 4, 4 => 5, 5 => 6], $result[1]);
        $this->assertEquals([6 => 7, 7 => 8, 8 => 9], $result[2]);
    }

    public function test_then_window_by_normal_size4(): void
    {
        $result = [];
        $enumerable = enumerate([1, 2, 3, 4, 5, 6, 7, 8, 9])
            ->then(windowBy(4));

        foreach ($enumerable as $key => $value) {
            $result[$key] = $value;
        }

        $this->assertCount(3, $result);
        $this->assertEquals([0 => 1, 1 => 2, 2 => 3, 3 => 4], $result[0]);
        $this->assertEquals([4 => 5, 5 => 6, 6 => 7, 7 => 8], $result[1]);
        $this->assertEquals([8 => 9], $result[2]);
    }

    public function test_then_window_by_normal_size5(): void
    {
        $result = [];
        $enumerable = enumerate([1, 2, 3, 4, 5, 6, 7, 8, 9])
            ->then(windowBy(5));

        foreach ($enumerable as $key => $value) {
            $result[$key] = $value;
        }

        $this->assertCount(2, $result);
        $this->assertEquals([0 => 1, 1 => 2, 2 => 3, 3 => 4, 4 => 5], $result[0]);
        $this->assertEquals([5 => 6, 6 => 7, 7 => 8, 8 => 9], $result[1]);
    }

    public function test_then_flat_map_by_empty(): void
    {
        $result = [];
        $enumerable = enumerate([])
            ->then(flatMapBy());

        foreach ($enumerable as $key => $value) {
            $result[$key] = $value;
        }

        $this->assertEquals([], $result);
    }

    public function test_then_flat_map_by_no_nesting(): void
    {
        $result = [];
        $enumerable = enumerate([1, 2, 3])
            ->then(flatMapBy());

        foreach ($enumerable as $key => $value) {
            $result[$key] = $value;
        }

        $this->assertEquals([1, 2, 3], $result);
    }

    public function test_then_flat_map_by_with_nesting(): void
    {
        $result = [];
        $enumerable = enumerate([1, [2, 3], 4])
            ->then(flatMapBy());

        foreach ($enumerable as $key => $value) {
            $result[$key] = $value;
        }

        $this->assertEquals([1, 2, 3, 4], $result);
    }

    public function test_then_flat_map_by_deep_nesting(): void
    {
        $result = [];
        $enumerable = enumerate([1, [2, [3, 4]], 5])
            ->then(flatMapBy());

        foreach ($enumerable as $key => $value) {
            $result[$key] = $value;
        }

        $this->assertEquals([1, 2, 3, 4, 5], $result);
    }

    public function test_then_flat_map_by_with_selector(): void
    {
        $result = [];
        $enumerable = enumerate([1, [2, 3], 4])
            ->then(flatMapBy(fn ($v): int => $v * 10));

        foreach ($enumerable as $key => $value) {
            $result[$key] = $value;
        }

        $this->assertEquals([10, 20, 30, 40], $result);
    }

    public function test_then_flat_map_by_exception_caught(): void
    {
        $result = [];
        $enumerable = enumerate([1, [2, 3], 4], fn (Throwable $e) => null)
            ->then(flatMapBy(fn ($v): int => $v === 2
                ? throw new Exception('Fail')
                : $v * 10));

        foreach ($enumerable as $key => $value) {
            $result[$key] = $value;
        }

        $this->assertEquals([10, 30, 40], $result);
    }

    public function test_then_flat_map_by_exception_thrown(): void
    {
        $result = [];

        $this->expectExceptionOfClassIn(Exception::class, function () use (&$result) {
            $enumerable = enumerate([1, [2, 3], 4])
                ->then(flatMapBy(fn ($v): int => $v === 2
                    ? throw new Exception('Fail')
                    : $v * 10));

            foreach ($enumerable as $key => $value) {
                $result[$key] = $value;
            }
        });

        $this->assertEquals([10], $result);
    }

    public function test_then_window_by_exception_caught(): void
    {
        $result = [];
        $enumerable = enumerate([1, 2, 3, 4, 5], fn (Throwable $e) => null)
            ->then(windowBy(2));

        foreach ($enumerable as $key => $value) {
            $result[$key] = $value;
        }

        $this->assertCount(3, $result);
    }

    public function test_then_window_by_exception_thrown(): void
    {
        $result = [];

        $enumerable = enumerate([1, 2, 3, 4, 5])
            ->then(windowBy(2));

        foreach ($enumerable as $key => $value) {
            $result[$key] = $value;
        }

        $this->assertCount(3, $result);
    }

    public function test_then_group_by_selector_exception_caught(): void
    {
        $result = [];
        $enumerable = enumerate([1, 2, 3, 4, 5, 6], fn (Throwable $e) => null)
            ->then(groupBy(fn (int $value) => $value === 3
                ? throw new Exception('Fail')
                : $value % 3));

        foreach ($enumerable as $key => $value) {
            $result[$key] = $value;
        }

        $this->assertCount(3, $result); // groups 0, 1, 2; only element 3 dropped
    }

    public function test_then_group_by_selector_exception_thrown(): void
    {
        $result = [];

        $this->expectExceptionOfClassIn(Exception::class, function () use (&$result) {
            $enumerable = enumerate([1, 2, 3, 4, 5, 6])
                ->then(groupBy(fn (int $value) => $value === 3
                    ? throw new Exception('Fail')
                    : $value % 3));

            foreach ($enumerable as $key => $value) {
                $result[$key] = $value;
            }
        });

        $this->assertEquals([], $result);
    }
}
