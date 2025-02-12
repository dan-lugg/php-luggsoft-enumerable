<?php

namespace Luggsoft\Enumerable\Functions;

use Exception;
use Luggsoft\Enumerable\TestCaseBase;
use Throwable;
use function Luggsoft\Enumerable\enumerate;

class IntoTest extends TestCaseBase
{
    public function testIntoArrayOfEmpty(): void
    {
        $result = enumerate([])
            ->into(arrayOf());

        $this->assertEquals([], $result);
    }

    public function testIntoArrayOfNormal(): void
    {
        $enumerable = enumerate([
            enumerate([1, 2, 3]),
            enumerate([4, 5, 6]),
        ]);

        $result = $enumerable->into(arrayOf());
        $this->assertEquals([[1, 2, 3], [4, 5, 6]], $result);
    }

    public function testIntoMinOfEmpty(): void
    {
        $result = enumerate([])
            ->into(minOf(fn(int $value) => $value));

        $this->assertNull($result);
    }

    public function testIntoMinOfNormalA(): void
    {
        $result = enumerate([1, 2, 3, 4, 5, 6])
            ->into(minOf(fn(int $value) => $value));

        $this->assertEquals(1, $result);
    }

    public function testIntoMinOfNormalB(): void
    {
        $result = enumerate([4, 5, 6, 1, 2, 3])
            ->into(minOf(fn(int $value) => $value));

        $this->assertEquals(1, $result);
    }

    public function testIntoMinOfExceptionCaught(): void
    {
        $result = enumerate([4, 5, 6, 1, 2, 3], fn(Throwable $e) => null)
            ->into(minOf(fn(int $value) => $value === 2
                ? throw new Exception("Fail")
                : $value));

        $this->assertEquals(1, $result);
    }

    public function testIntoMinOfExceptionThrown(): void
    {
        $result = null;

        $this->expectExceptionOfClassIn(Exception::class, function () use (&$result) {
            $result = enumerate([4, 5, 6, 1, 2, 3])
                ->into(minOf(fn(int $value) => $value === 2
                    ? throw new Exception("Fail")
                    : $value));
        });

        $this->assertEquals(null, $result);
    }

    public function testIntoMinKeyOfEmpty(): void
    {
        $result = enumerate([])
            ->into(minKeyOf(fn(int $value) => $value));

        $this->assertNull($result);
    }

    public function testIntoMinKeyOfNormalA(): void
    {
        $result = enumerate([1, 2, 3, 4, 5, 6])
            ->into(minKeyOf());

        $this->assertEquals(0, $result);
    }

    public function testIntoMinKeyOfNormalB(): void
    {
        $result = enumerate([4, 5, 6, 1, 2, 3])
            ->into(minKeyOf());

        $this->assertEquals(3, $result);
    }

    public function testIntoMinKeyOfExceptionCaught(): void
    {
        $result = enumerate([4, 5, 6, 1, 2, 3], fn(Throwable $e) => null)
            ->into(minKeyOf(fn(int $value) => $value === 2
                ? throw new Exception("Fail")
                : $value));

        $this->assertEquals(3, $result);
    }

    public function testIntoMinKeyOfExceptionThrown(): void
    {
        $result = null;

        $this->expectExceptionOfClassIn(Exception::class, function () use (&$result) {
            $result = enumerate([4, 5, 6, 1, 2, 3])
                ->into(minKeyOf(fn(int $value) => $value !== 2 ? $value : throw new Exception("Fail")));
        });

        $this->assertEquals(null, $result);
    }

    public function testIntoMaxOfEmpty(): void
    {
        $result = enumerate([])
            ->into(maxOf(fn(int $value) => $value));

        $this->assertNull($result);
    }

    public function testIntoMaxOfNormalA(): void
    {
        $result = enumerate([1, 2, 3, 4, 5, 6])
            ->into(maxOf(fn(int $value) => $value));

        $this->assertEquals(6, $result);
    }

    public function testIntoMaxOfNormalB(): void
    {
        $result = enumerate([4, 5, 6, 1, 2, 3])
            ->into(maxOf(fn(int $value) => $value));

        $this->assertEquals(6, $result);
    }

    public function testIntoMaxOfExceptionCaught(): void
    {
        $result = enumerate([4, 5, 6, 1, 2, 3], fn(Throwable $e) => null)
            ->into(maxOf(fn(int $value) => $value === 2
                ? throw new Exception("Fail")
                : $value));

        $this->assertEquals(6, $result);
    }

    public function testIntoMaxOfExceptionThrown(): void
    {
        $result = null;

        $this->expectExceptionOfClassIn(Exception::class, function () use (&$result) {
            $result = enumerate([4, 5, 6, 1, 2, 3])
                ->into(maxOf(fn(int $value) => $value === 2
                    ? throw new Exception("Fail")
                    : $value));
        });

        $this->assertEquals(null, $result);
    }

    public function testIntoDoneEmpty(): void
    {
        $result = enumerate([1, 2, 3])
            ->into(done());

        $this->assertNull($result);
    }

    public function testIntoDoneNormal(): void
    {
        $result = enumerate([1, 2, 3])
            ->then(mapBy(fn(int $value) => $value % 3))
            ->into(done());

        $this->assertNull($result);
    }

    public function testIntoDoneNormalCapturing(): void
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

    public function testIntoDoneCapturingExceptionCaught(): void
    {
        $captured = [];

        $result = enumerate([1, 2, 3], fn(Throwable $e) => null)
            ->then(forEachWith(function (int $value) use (&$captured) {
                if ($value === 2) {
                    throw new Exception("Fail");
                }
                $captured[] = $value;
            }))
            ->into(done());

        $this->assertNull($result);
        $this->assertEquals([1, 3], $captured);
    }

    public function testIntoDoneCapturingExceptionThrown(): void
    {
        $result = null;
        $captured = [];

        $this->expectExceptionOfClassIn(Exception::class, function () use (&$result, &$captured) {
            $result = enumerate([1, 2, 3])
                ->then(forEachWith(function (int $value) use (&$captured) {
                    if ($value === 2) {
                        throw new Exception("Fail");
                    }

                    $captured[] = $value;
                }))
                ->into(done());
        });

        $this->assertEquals(null, $result);
        $this->assertEquals([1], $captured);
    }
}