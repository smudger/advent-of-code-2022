<?php

namespace Smudger\AdventOfCode2022\Tests\Day1;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Smudger\AdventOfCode2022\Day1\Puzzle1;

class Puzzle1Test extends TestCase
{
    /** @test */
    public function it_returns_the_correct_answer_for_example_1(): void
    {
        Assert::assertEquals(24000, (new Puzzle1())('example1.txt'));
    }
}
