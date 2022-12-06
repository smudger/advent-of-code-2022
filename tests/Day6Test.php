<?php

namespace Smudger\AdventOfCode2022\Tests;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Smudger\AdventOfCode2022\Day6\Puzzle1;
use Smudger\AdventOfCode2022\Day6\Puzzle2;

class Day6Test extends TestCase
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
            ['example1.txt', 7],
            ['example2.txt', 5],
            ['example3.txt', 6],
            ['example4.txt', 10],
            ['example5.txt', 11],
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
            ['example6.txt', 19],
            ['example7.txt', 23],
            ['example8.txt', 23],
            ['example9.txt', 29],
            ['example10.txt', 26],
        ];
    }
}
