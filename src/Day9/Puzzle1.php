<?php

namespace Smudger\AdventOfCode2022\Day9;

use Exception;
use Illuminate\Support\Collection;

class Puzzle1
{
    public function __invoke(string $fileName)
    {
        $input = file_get_contents(__DIR__.'/'.$fileName)
            ?: throw new Exception('Failed to read input file.');
        $moves = (new Collection(explode("\n", $input)))
            ->flatMap(function (string $line) {
                [$direction, $count] = explode(' ', $line);

                return array_fill(0, intval($count), $direction);
            })
            ->all();

        $visitedCells = [];
        $headPosition = new Position(0, 0);
        $tailPosition = new Position(0, 0);

        foreach ($moves as $move) {
            $headPosition->move($move);
            $tailPosition->reconcileWith($headPosition);

            $hasVisitedBefore = count(array_filter($visitedCells, fn (Position $position) => $tailPosition->equals($position))) !== 0;

            if (! $hasVisitedBefore) {
                $visitedCells[] = $tailPosition->clone();
            }
        }

        return count($visitedCells);
    }
}
