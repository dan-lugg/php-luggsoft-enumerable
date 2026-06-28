<?php

namespace Luggsoft\Enumerable;

use ArrayIterator;

class EnumerateTest extends TestCaseBase
{
    public function test_enumerate_with_array(): void
    {
        $result = [];
        $enumerable = enumerate([1, 2, 3]);

        foreach ($enumerable as $value) {
            $result[] = $value;
        }

        $this->assertEquals([1, 2, 3], $result);
    }

    public function test_enumerate_with_iterator(): void
    {
        $result = [];
        $enumerable = enumerate(new ArrayIterator([1, 2, 3]));

        foreach ($enumerable as $value) {
            $result[] = $value;
        }

        $this->assertEquals([1, 2, 3], $result);
    }

    public function test_enumerate_with_generator(): void
    {
        $result = [];
        $enumerable = enumerate(function () {
            foreach ([1, 2, 3] as $value) {
                yield $value;
            }
        });
        foreach ($enumerable as $value) {
            $result[] = $value;
        }
        $this->assertEquals([1, 2, 3], $result);
    }

    public function test_enumerate_with_array_empty(): void
    {
        $result = [];
        $enumerable = enumerate([]);

        foreach ($enumerable as $value) {
            $result[] = $value;
        }

        $this->assertEquals([], $result);
    }

    public function test_enumerate_with_iterator_empty(): void
    {
        $result = [];
        $enumerable = enumerate(new ArrayIterator());

        foreach ($enumerable as $value) {
            $result[] = $value;
        }

        $this->assertEquals([], $result);
    }

    public function test_enumerate_with_generator_empty(): void
    {
        $result = [];

        $enumerable = enumerate(function () {
            if (false) {
                /** @noinspection PhpUnreachableStatementInspection */
                yield 0;
            }
        });

        foreach ($enumerable as $value) {
            $result[] = $value;
        }

        $this->assertEquals([], $result);
    }
}
