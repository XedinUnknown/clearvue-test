<?php

namespace Clearvue\Test1\Test\Func;

use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;

class ThingTest extends TestCase
{
    public function testSomething()
    {
        $this->expectException(ExpectationFailedException::class);
        $this->assertTrue(false);
    }
}
