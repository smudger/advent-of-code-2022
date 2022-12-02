<?php

namespace Smudger\AdventOfCode2022\Day2;

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
            ->map(fn (string $pair) => explode(' ', $pair))
            ->map(fn (array $pair) => array_map(fn (string $letter) => $this->toInt($letter), $pair))
            ->map(fn (array $pair) => [$pair[1] - $pair[0], $pair[1]])
            ->map(fn (array $pair) => [($pair[0] + 3) % 3, $pair[1]])
            ->map(fn (array $pair) => [$this->score($pair[0]), $pair[1]])
            ->map(fn (array $pair) => $pair[0] + $pair[1])
            ->sum();
    }

    private function toInt(string $letter): int
    {
        return match ($letter) {
            'A', 'X' => 1,
            'B', 'Y' => 2,
            'C', 'Z' => 3,
        };
    }

    private function score(int $mod): int
    {
        return match ($mod) {
            0 => 3,
            1 => 6,
            2 => 0,
        };
    }
}
