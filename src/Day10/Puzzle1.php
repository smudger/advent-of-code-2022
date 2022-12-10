<?php

namespace Smudger\AdventOfCode2022\Day10;

use Exception;
use Illuminate\Support\Collection;

class Puzzle1
{
    public function __invoke(string $fileName)
    {
        $input = file_get_contents(__DIR__.'/'.$fileName)
            ?: throw new Exception('Failed to read input file.');
        $commands = explode("\n", $input);

        return (new Collection($commands))
            ->reduce(function (Collection $carry, string $command) {
                if ($command === 'noop') {
                    return $carry->push($carry->last());
                }

                $change = intval(explode(' ', $command)[1]);
                $carry->push($carry->last());

                return $carry->push($carry->last() + $change);
            }, new Collection([0, 1]))
            ->map(fn (int $register, int $cycle) => $register * $cycle)
            ->filter(fn (int $_, int $cycle) => in_array($cycle, [20, 60, 100, 140, 180, 220]))
            ->sum();
    }
}
