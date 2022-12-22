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
            default => throw new \Exception('NO GLUE FOR EDGE: '.'['.implode(', ', $position).']'),
        };
    }
}
