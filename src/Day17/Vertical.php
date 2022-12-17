<?php

namespace Smudger\AdventOfCode2022\Day17;

class Vertical extends Rock
{
    private array $positions;

    public function spawnAt(int $baseHeight): void
    {
        $this->positions = [
            $baseHeight => [
                3,
            ],
            $baseHeight + 1 => [
                3,
            ],
            $baseHeight + 2 => [
                3,
            ],
            $baseHeight + 3 => [
                3,
            ],
        ];
    }

    public function getPositions(): array
    {
        return $this->positions;
    }

    public function setPositions(array $positions): void
    {
        $this->positions = $positions;
    }
}
