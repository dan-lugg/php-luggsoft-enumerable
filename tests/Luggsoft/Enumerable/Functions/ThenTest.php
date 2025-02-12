<?php

namespace Luggsoft\Enumerable\Functions;

use Exception;
use Luggsoft\Enumerable\TestCaseBase;
use Throwable;
use function Luggsoft\Enumerable\enumerate;

class ThenTest extends TestCaseBase
{
    public function testThenMapByEmpty(): void
    {
        $result = [];
        $enumerable = enumerate([])
            ->then(mapBy(fn(int $value) => $value * 3));

        foreach ($enumerable as $key => $value) {
            $result[$key] = $value;
        }

        $this->assertEquals([], $result);
    }

    public function testThenMapByNormal(): void
    {
        $result = [];
        $enumerable = enumerate([0 => 1, 1 => 2, 2 => 3])
            ->then(mapBy(fn(int $value) => $value * 3));

        foreach ($enumerable as $key => $value) {
            $result[$key] = $value;
        }

        $this->assertEquals([0 => 3, 1 => 6, 2 => 9], $result);
    }

    public function testThenMapByExceptionCaught(): void
    {
        $result = [];
        $enumerable = enumerate([0 => 1, 1 => 2, 2 => 3], fn(Throwable $e) => null)
            ->then(mapBy(fn(int $value) => $value === 2
                ? throw new Exception("Fail")
                : $value));

        foreach ($enumerable as $key => $value) {
            $result[$key] = $value;
        }

        $this->assertEquals([0 => 1, 2 => 3], $result);
    }

    public function testThenMapByExceptionThrown(): void
    {
        $result = [];

        $this->expectExceptionOfClassIn(Exception::class, function () use (&$result) {
            $enumerable = enumerate([0 => 1, 1 => 2, 2 => 3])
                ->then(mapBy(fn(int $value) => $value === 2
                    ? throw new Exception("Fail")
                    : $value));

            foreach ($enumerable as $key => $value) {
                $result[$key] = $value;
            }
        });

        $this->assertEquals([0 => 1], $result);
    }

    public function testThenMapKeysByEmpty(): void
    {
        $result = [];
        $enumerable = enumerate([])
            ->then(mapKeysBy(fn(int $value) => "key_$value"));

        foreach ($enumerable as $key => $value) {
            $result[$key] = $value;
        }

        $this->assertEquals([], $result);
    }

    public function testThenMapKeysByNormal(): void
    {
        $result = [];
        $enumerable = enumerate([1, 2, 3])
            ->then(mapKeysBy(fn(int $value) => "key_$value"));

        foreach ($enumerable as $key => $value) {
            $result[$key] = $value;
        }

        $this->assertEquals(["key_1" => 1, "key_2" => 2, "key_3" => 3], $result);
    }

    public function testThenMapKeysByExceptionCaught(): void
    {
        $result = [];
        $enumerable = enumerate([0 => 1, 1 => 2, 2 => 3], fn(Throwable $e) => null)
            ->then(mapKeysBy(fn(int $value) => ($value === 2
                ? throw new Exception("Fail")
                : "key_$value")));

        foreach ($enumerable as $key => $value) {
            $result[$key] = $value;
        }

        $this->assertEquals(["key_1" => 1, "key_3" => 3], $result);
    }

    public function testThenMapKeysByExceptionThrown(): void
    {
        $result = [];

        $this->expectExceptionOfClassIn(Exception::class, function () use (&$result) {
            $enumerable = enumerate([0 => 1, 1 => 2, 2 => 3])
                ->then(mapKeysBy(fn(int $value) => $value === 2
                    ? throw new Exception("Fail")
                    : "key_$value"));

            foreach ($enumerable as $key => $value) {
                $result[$key] = $value;
            }
        });

        $this->assertEquals(["key_1" => 1], $result);
    }

    public function testThenFilterByDefaultPredicate(): void
    {
        $result = [];

        $enumerable = enumerate([])
            ->then(filterBy());

        foreach ($enumerable as $key => $value) {
            $result[$key] = $value;
        }

        $this->assertEquals([], $result);
    }

    public function testThenFilterByEmpty(): void
    {
        $result = [];

        $enumerable = enumerate([])
            ->then(filterBy(fn(int $value) => $value * 3));

        foreach ($enumerable as $key => $value) {
            $result[$key] = $value;
        }

        $this->assertEquals([], $result);
    }

    public function testThenFilterByNormal(): void
    {
        $result = [];

        $enumerable = enumerate([0 => 1, 1 => 2, 2 => 3, 3 => 4])
            ->then(filterBy(fn(int $value) => $value % 2 === 0));

        foreach ($enumerable as $key => $value) {
            $result[$key] = $value;
        }

        $this->assertEquals([1 => 2, 3 => 4], $result);
    }

    public function testThenFilterByExceptionCaught(): void
    {
        $result = [];

        $enumerable = enumerate([0 => 1, 1 => 2, 2 => 3, 3 => 4], fn(Throwable $e) => null)
            ->then(filterBy(fn(int $value) => $value === 2
                ? throw new Exception("Fail")
                : $value % 2 === 0));

        foreach ($enumerable as $key => $value) {
            $result[$key] = $value;
        }

        $this->assertEquals([3 => 4], $result);
    }

    public function testThenFilterByExceptionThrown(): void
    {
        $result = [];

        $this->expectExceptionOfClassIn(Exception::class, function () use (&$result) {
            $enumerable = enumerate([0 => 1, 1 => 2, 2 => 3])
                ->then(filterBy(fn(int $value) => $value === 2
                    ? throw new Exception("Fail")
                    : ($value % 2 === 0)));

            foreach ($enumerable as $key => $value) {
                $result[$key] = $value;
            }
        });

        $this->assertEquals([], $result);
    }

    public function testThenTakeWhileDefaultPredicate(): void
    {
        $result = [];
        $enumerable = enumerate([])
            ->then(takeWhile());

        foreach ($enumerable as $key => $value) {
            $result[$key] = $value;
        }

        $this->assertEquals([], $result);
    }

    public function testThenTakeWhileEmpty(): void
    {
        $result = [];
        $enumerable = enumerate([])
            ->then(takeWhile(fn(int $value) => $value <= 3));

        foreach ($enumerable as $key => $value) {
            $result[$key] = $value;
        }

        $this->assertEquals([], $result);
    }

    public function testThenTakeWhileNormal(): void
    {
        $result = [];
        $enumerable = enumerate([1, 2, 3, 4, 5, 6])
            ->then(takeWhile(fn(int $value) => $value <= 3));

        foreach ($enumerable as $key => $value) {
            $result[$key] = $value;
        }

        $this->assertEquals([1, 2, 3], $result);
    }

    public function testThenTakeWhileExceptionCaught(): void
    {
        $result = [];
        $enumerable = enumerate([1, 2, 3, 4, 5, 6], fn(Throwable $e) => null)
            ->then(takeWhile(fn(int $value) => $value === 2
                ? throw new Exception("Fail")
                : $value <= 3));

        foreach ($enumerable as $key => $value) {
            $result[$key] = $value;
        }

        $this->assertEquals([0 => 1, 2 => 3], $result);
    }

    public function testThenTakeWhileExceptionThrown(): void
    {
        $result = [];

        $this->expectExceptionOfClassIn(Exception::class, function () use (&$result) {
            $enumerable = enumerate([0 => 1, 1 => 2, 2 => 3])
                ->then(takeWhile(fn(int $value) => $value === 2
                    ? throw new Exception("Fail")
                    : $value <= 3));

            foreach ($enumerable as $key => $value) {
                $result[$key] = $value;
            }
        });

        $this->assertEquals([0 => 1], $result);
    }

    public function testThenDropWhileDefaultPredicate(): void
    {
        $result = [];
        $enumerable = enumerate([])
            ->then(dropWhile());

        foreach ($enumerable as $key => $value) {
            $result[$key] = $value;
        }

        $this->assertEquals([], $result);
    }

    public function testThenDropWhileEmpty(): void
    {
        $result = [];
        $enumerable = enumerate([])
            ->then(dropWhile(fn(int $value) => $value <= 3));

        foreach ($enumerable as $key => $value) {
            $result[$key] = $value;
        }

        $this->assertEquals([], $result);
    }

    public function testThenDropWhileNormal(): void
    {
        $result = [];
        $enumerable = enumerate([1, 2, 3, 4, 5, 6])
            ->then(dropWhile(fn(int $value) => $value <= 3));

        foreach ($enumerable as $key => $value) {
            $result[$key] = $value;
        }

        $this->assertEquals([3 => 4, 4 => 5, 5 => 6], $result);
    }

    public function testThenDropWhileExceptionCaught(): void
    {
        $result = [];
        $enumerable = enumerate([1, 2, 3, 4, 5, 6], fn(Throwable $e) => null)
            ->then(dropWhile(fn(int $value) => $value === 2
                ? throw new Exception("Fail")
                : $value <= 3));

        foreach ($enumerable as $key => $value) {
            $result[$key] = $value;
        }

        $this->assertEquals([3 => 4, 4 => 5, 5 => 6], $result);
    }

    public function testThenDropWhileExceptionThrown(): void
    {
        $result = [];

        $this->expectExceptionOfClassIn(Exception::class, function () use (&$result) {
            $enumerable = enumerate([0 => 1, 1 => 2, 2 => 3])
                ->then(dropWhile(fn(int $value) => $value === 2
                    ? throw new Exception("Fail")
                    : $value <= 3));

            foreach ($enumerable as $key => $value) {
                $result[$key] = $value;
            }
        });

        $this->assertEquals([], $result);
    }

    public function testThenGroupByEmpty(): void
    {
        $result = [];
        $enumerable = enumerate([])
            ->then(groupBy(fn(int $value) => $value % 3));

        foreach ($enumerable as $key => $value) {
            $result[$key] = $value;
        }

        $this->assertEquals([], $result);
    }

    public function testThenGroupByNormal(): void
    {
        $result = [];
        $enumerable = enumerate([1, 2, 3, 4, 5, 6, 7, 8, 9])
            ->then(groupBy(fn(int $value) => $value % 3));

        foreach ($enumerable as $key => $value) {
            $result[$key] = $value;
        }

        $this->assertEquals([2 => 3, 5 => 6, 8 => 9], $result[0]);
        $this->assertEquals([0 => 1, 3 => 4, 6 => 7], $result[1]);
        $this->assertEquals([1 => 2, 4 => 5, 7 => 8], $result[2]);
    }

    public function testThenGroupByExceptionCaught(): void
    {
        $result = [];
        $enumerable = enumerate([1, 2, 3, 4, 5, 6], fn(Throwable $e) => null)
            ->then(groupBy(fn(int $value) => $value === 2
                ? throw new Exception("Fail")
                : $value % 3));

        foreach ($enumerable as $key => $value) {
            $result[$key] = $value;
        }

        $this->assertEquals([2 => 3, 5 => 6], $result[0]);
        $this->assertEquals([0 => 1, 3 => 4], $result[1]);
        $this->assertEquals([4 => 5], $result[2]);
    }

    public function testThenGroupByExceptionThrown(): void
    {
        $result = [];

        $this->expectExceptionOfClassIn(Exception::class, function () use (&$result) {
            $enumerable = enumerate([0 => 1, 1 => 2, 2 => 3])
                ->then(groupBy(fn(int $value) => $value === 2
                    ? throw new Exception("Fail")
                    : $value % 3));

            foreach ($enumerable as $key => $value) {
                $result[$key] = $value;
            }
        });

        $this->assertEquals([], $result);
    }

    public function testThenWindowByEmpty(): void
    {
        $result = [];
        $enumerable = enumerate([])
            ->then(windowBy(3));

        foreach ($enumerable as $key => $value) {
            $result[$key] = $value;
        }

        $this->assertEquals([], $result);
    }

    public function testThenWindowByNormalSize3(): void
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

    public function testThenWindowByNormalSize4(): void
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

    public function testThenWindowByNormalSize5(): void
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
}