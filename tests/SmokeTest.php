<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;

class SmokeTest extends TestCase
{
    public function testAdditionWorks(): void
    {
        // Arrange(準備)
        $a = 2;
        $b = 3;

        // Act(実行)
        $result = $a + $b;

        // Assert(検証)
        $this->assertEquals(5, $result);
    }
}