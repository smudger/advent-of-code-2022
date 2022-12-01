<?php

namespace Smudger\AdventOfCode2022\Day1;

use Exception;
use Illuminate\Support\Collection;

class Puzzle2
{
    public function __invoke(string $fileName): int
    {
        $input = file_get_contents(__DIR__.'/'.$fileName)
            ?: throw new Exception('Failed to read input file.');

        return (new Collection(explode("\n", $input)))
            ->reduce(function (Collection $carry, string $element) {
                return $carry->tap(function (Collection $carry) use ($element) {
                    $element !== ''
                        ? $carry->last()->push($element)
                        : $carry->push(new Collection());
                });
            }, new Collection([new Collection()]))
            ->filter->isNotEmpty()
            ->map->sum()
            ->sortDesc()
            ->take(3)
            ->sum();
    }
}
