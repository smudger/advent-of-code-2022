<?php

namespace Smudger\AdventOfCode2022\Day22;

class Glue
{
    public function __construct(private readonly string $fileName)
    {
    }

    public function stick(array $position): array
    {
        return match ($this->fileName) {
            'example1.txt' => $this->example1($position),
            'input.txt' => $this->input($position),
            default => throw new \Exception('UNRECOGNISED FILE: '.$this->fileName),
        };
    }

    private function example1(array $position): array
    {
        return match (true) {
            $position[2] === 0 && $position[1] === 13 && $position[0] >= 5 && $position[0] <= 8 => [9, 21 - $position[0], 1],
            $position[2] === 1 && $position[0] === 13 && $position[1] >= 9 && $position[1] <= 12 => [8, 13 - $position[1], 3],
            $position[2] === 3 && $position[0] === 4 && $position[1] >= 5 && $position[1] <= 8 => [$position[1] - 4, 9, 0],
            default => throw new \Exception('NO GLUE FOR EDGE: '.'['.implode(', ', $position).']'),
        };
    }

    private function input(array $position): array
    {
        return match (true) {
            $position[2] === 2 && $position[1] === 50 && $position[0] >= 1 && $position[0] <= 50 => [151 - $position[0], 1, 0],
            $position[2] === 2 && $position[1] === 0 && $position[0] >= 101 && $position[0] <= 150 => [151 - $position[0], 51, 0],
            $position[2] === 1 && $position[0] === 151 && $position[1] >= 51 && $position[1] <= 100 => [100 + $position[1], 50, 2],
            $position[2] === 3 && $position[0] === 0 && $position[1] >= 51 && $position[1] <= 100 => [100 + $position[1], 1, 0],
            $position[2] === 0 && $position[1] === 101 && $position[0] >= 51 && $position[0] <= 100 => [50, 50 + $position[0], 3],
            $position[2] === 0 && $position[1] === 151 && $position[0] >= 1 && $position[0] <= 50 => [151 - $position[0], 100, 2],
            $position[2] === 2 && $position[1] === 50 && $position[0] >= 51 && $position[0] <= 100 => [101, $position[0] - 50, 1],
            $position[2] === 3 && $position[0] === 100 && $position[1] >= 1 && $position[1] <= 50 => [50 + $position[1], 51, 0],
            $position[2] === 0 && $position[1] === 101 && $position[0] >= 101 && $position[0] <= 150 => [151 - $position[0], 150, 2],
            $position[2] === 2 && $position[1] === 0 && $position[0] >= 151 && $position[0] <= 200 => [1, $position[0] - 100, 1],
            $position[2] === 1 && $position[0] === 51 && $position[1] >= 101 && $position[1] <= 150 => [$position[1] - 50, 100, 2],
            $position[2] === 3 && $position[0] === 0 && $position[1] >= 101 && $position[1] <= 150 => [200, $position[1] - 100, 3],
            $position[2] === 1 && $position[0] === 201 && $position[1] >= 1 && $position[1] <= 50 => [1, 100 + $position[1], 1],
            $position[2] === 0 && $position[1] === 51 && $position[0] >= 151 && $position[0] <= 200 => [150, $position[0] - 100, 3],
            default => throw new \Exception('NO GLUE FOR EDGE: '.'['.implode(', ', $position).']'),
        };
    }
}
