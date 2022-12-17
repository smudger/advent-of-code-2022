<?php

namespace Smudger\AdventOfCode2022\Day17;

class Plus extends Rock
{
    private array $positions;

    public function spawnAt(int $baseHeight): void
    {
        $this->positions = [
            $baseHeight => [
                4,
            ],
            $baseHeight + 1 => [
                3,
                4,
                5,
            ],
            $baseHeight + 2 => [
                4,
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
