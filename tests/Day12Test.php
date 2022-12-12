<?php

namespace Smudger\AdventOfCode2022\Tests;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Smudger\AdventOfCode2022\Day12\Puzzle1;
use Smudger\AdventOfCode2022\Day12\Puzzle2;

class Day12Test extends TestCase
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
            ['example1.txt', 31],
            ['input.txt', 490],
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
            ['example1.txt', 19],
        ];
    }
}
