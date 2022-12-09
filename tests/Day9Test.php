<?php

namespace Smudger\AdventOfCode2022\Tests;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Smudger\AdventOfCode2022\Day9\Puzzle1;
use Smudger\AdventOfCode2022\Day9\Puzzle2;

class Day9Test extends TestCase
{
    /**
     * @test
     * @dataProvider getPuzzle1Examples()
     */
    public function it_returns_the_correct_answer_for_puzzle_1($example, $answer): void
    {
        Assert::assertEquals($answer, (new Puzzle1())($example));
    }

    public function getPuzzle1Examples()
    {
        return [
            ['example1.txt', 13],
            ['input.txt', 6284],
        ];
    }

    /**
     * @test
     * @dataProvider getPuzzle2Examples()
     */
    public function it_returns_the_correct_answer_for_puzzle_2($example, $answer): void
    {
        Assert::assertEquals($answer, (new Puzzle2())($example));
    }

    public function getPuzzle2Examples()
    {
        return [
            ['example1.txt', 1],
            ['example2.txt', 36],
            ['input.txt', 2661],
        ];
    }
}
