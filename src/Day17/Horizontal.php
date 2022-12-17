<?php

namespace Smudger\AdventOfCode2022\Day17;

class Horizontal extends Rock
{
    private array $positions;

    public function spawnAt(int $baseHeight): void
    {
        $this->positions = [
            $baseHeight => [
                3,
                4,
                5,
                6,
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
