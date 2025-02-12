<?php

namespace Luggsoft\Enumerable;

use PHPUnit\Framework\TestCase;
use Throwable;

abstract class TestCaseBase extends TestCase
{
    /**
     * @param class-string $exceptionClass
     * @param callable $block
     * @return void
     */
    public function expectExceptionOfClassIn(string $exceptionClass, callable $block): void
    {
        try {
            $block();
            $this->fail(vsprintf("Expected exception of type %s, but no exception thrown.", [
                $exceptionClass,
            ]));
        }
        catch (Throwable $exception) {
            $this->assertInstanceOf($exceptionClass, $exception);
        }
    }
}