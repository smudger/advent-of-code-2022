<?php

namespace Smudger\AdventOfCode2022\Day4;

use Exception;
use Illuminate\Support\Collection;

class Puzzle1
{
    public function __invoke(string $fileName): int
    {
        $input = file_get_contents(__DIR__.'/'.$fileName)
            ?: throw new Exception('Failed to read input file.');

        return (new Collection(explode("\n", $input)))
            ->filter()
            ->map(fn (string $ranges) => new Collection(explode(',', $ranges)))
            ->map(fn (Collection $ranges) => $ranges->map(fn (string $range) => array_map('intval', explode('-', $range))))
            ->map(fn (Collection $ranges) => $ranges->map(fn (array $pair) => range($pair[0], $pair[1])))
            ->filter(fn (Collection $ranges) => array_values(array_intersect($ranges[0], $ranges[1])) === $ranges[0] || array_values(array_intersect($ranges[0], $ranges[1])) === $ranges[1])
            ->count();
    }
}
