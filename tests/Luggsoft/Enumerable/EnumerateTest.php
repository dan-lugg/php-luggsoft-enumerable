<?php

namespace Luggsoft\Enumerable;

use ArrayIterator;

class EnumerateTest extends TestCaseBase
{
    public function testEnumerateWithArray(): void
    {
        $result = [];
        $enumerable = enumerate([1, 2, 3]);

        foreach ($enumerable as $value) {
            $result[] = $value;
        }

        $this->assertEquals([1, 2, 3], $result);
    }

    public function testEnumerateWithIterator(): void
    {
        $result = [];
        $enumerable = enumerate(new ArrayIterator([1, 2, 3]));

        foreach ($enumerable as $value) {
            $result[] = $value;
        }

        $this->assertEquals([1, 2, 3], $result);
    }

    public function testEnumerateWithGenerator(): void
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

    public function testEnumerateWithArrayEmpty(): void
    {
        $result = [];
        $enumerable = enumerate([]);

        foreach ($enumerable as $value) {
            $result[] = $value;
        }

        $this->assertEquals([], $result);
    }

    public function testEnumerateWithIteratorEmpty(): void
    {
        $result = [];
        $enumerable = enumerate(new ArrayIterator());

        foreach ($enumerable as $value) {
            $result[] = $value;
        }

        $this->assertEquals([], $result);
    }

    public function testEnumerateWithGeneratorEmpty(): void
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
