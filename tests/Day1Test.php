<?php

namespace Smudger\AdventOfCode2022\Tests;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Smudger\AdventOfCode2022\Day1\Puzzle1;
use Smudger\AdventOfCode2022\Day1\Puzzle2;

class Day1Test extends TestCase
{
    /** @test */
    public function it_returns_the_correct_answer_for_puzzle_1_example_1(): void
    {
        Assert::assertEquals(24000, (new Puzzle1())('example1.txt'));
    }

    /** @test */
    public function it_returns_the_correct_answer_for_puzzle_2_example_1(): void
    {
        Assert::assertEquals(45000, (new Puzzle2())('example1.txt'));
    }
}
