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

        $headPosition = new Position(0, 0);
        $tailPosition = new Position(0, 0);

        return (new Collection($moves))
            ->reduce(function (Collection $carry, string $move) use ($headPosition, $tailPosition) {
                $headPosition->move($move);
                $tailPosition->reconcileWith($headPosition);

                $carry->push($tailPosition->clone());

                return $carry;
            }, new Collection([]))
            ->unique(fn (Position $position) => $position->__toString())
            ->count();
    }
}
