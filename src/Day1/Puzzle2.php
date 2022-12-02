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

        return (new Collection(explode("\n\n", $input)))
            ->map(fn (string $group) => new Collection(explode("\n", $group)))
            ->map(fn (Collection $group) => $group->filter())
            ->map->sum()
            ->sortDesc()
            ->take(3)
            ->sum();
    }
}
