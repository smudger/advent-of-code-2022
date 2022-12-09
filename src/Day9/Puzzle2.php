<?php

namespace Smudger\AdventOfCode2022\Day9;

use Exception;
use Illuminate\Support\Collection;

class Puzzle2
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
        $knots = array_map(fn () => new Position(0, 0), range(0, 9));

        foreach ($moves as $move) {
            $knots[0]->move($move);
            foreach ($knots as $i => $knot) {
                if ($i === 0) {
                    continue;
                }

                $knot->reconcileWith($knots[$i - 1]);
            }

            $hasVisitedBefore = count(array_filter($visitedCells, fn (Position $position) => end($knots)->equals($position))) !== 0;

            if (! $hasVisitedBefore) {
                $visitedCells[] = end($knots)->clone();
            }
        }

        return count($visitedCells);
    }
}
