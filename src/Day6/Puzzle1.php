<?php

namespace Smudger\AdventOfCode2022\Day6;

use Exception;
use Illuminate\Support\Collection;

class Puzzle1
{
    public function __invoke(string $fileName): int
    {
        $input = file_get_contents(__DIR__.'/'.$fileName)
            ?: throw new Exception('Failed to read input file.');
        $buffer = str_split($input);
        $startIndex = (new Collection($buffer))
            ->map(fn (string $_, int $index) => array_splice($buffer, $index, 4))
            ->where(fn (array $splice) => array_unique($splice) === $splice)
            ->keys()
            ->first();

        return $startIndex + 4;
    }
}
