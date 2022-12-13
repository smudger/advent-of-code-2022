<?php

namespace Smudger\AdventOfCode2022\Day13;

use Exception;
use Illuminate\Support\Collection;

class Puzzle1
{
    public function __invoke(string $fileName)
    {
        $input = file_get_contents(__DIR__.'/'.$fileName)
            ?: throw new Exception('Failed to read input file.');
        $comparator = new Comparator();

        return (new Collection(explode("\n\n", $input)))
            ->map(fn (string $pair) => array_map(fn (string $array) => json_decode($array, true), explode("\n", $pair)))
            ->filter(fn (array $pair) => $comparator->compare($pair[0], $pair[1]) === 1)
            ->map(fn (array $pair, int $index) => $index + 1)
            ->sum();
    }
}
