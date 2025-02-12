<?php

namespace Luggsoft\Enumerable;

class GeneratedTest extends TestCaseBase
{
    public function testGeneratedNormal(): void
    {
        $generated = generated(function () {
            foreach ([1, 2, 3] as $value) {
                yield $value;
            }
        });

        $result = [];

        foreach ($generated as $value) {
            $result[] = $value;
        }

        $this->assertEquals([1, 2, 3], $result);

        $result = [];

        foreach ($generated as $value) {
            $result[] = $value;
        }

        $this->assertEquals([1, 2, 3], $result);

        $result = [];

        foreach ($generated as $value) {
            $result[] = $value;
        }

        $this->assertEquals([1, 2, 3], $result);
    }
}
