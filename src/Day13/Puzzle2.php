<?php

namespace Smudger\AdventOfCode2022\Day13;

use Exception;
use Illuminate\Support\Collection;

class Puzzle2
{
    public function __invoke(string $fileName)
    {
        $input = file_get_contents(__DIR__.'/'.$fileName)
            ?: throw new Exception('Failed to read input file.');
        $comparator = new Comparator();

        return (new Collection(explode("\n", $input)))
            ->filter()
            ->map(fn (string $row) => json_decode($row, true))
            ->merge([[[2]], [[6]]])
            ->sort(fn ($left, $right) => $comparator->compare($left, $right))
            ->values()
            ->filter(fn ($row) => $row === [[2]] || $row === [[6]])
            ->reduce(fn (int $carry, $_, int $index) => $carry * ($index + 1), 1);
    }
}
