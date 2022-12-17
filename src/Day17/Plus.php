<?php

namespace Smudger\AdventOfCode2022\Day17;

use Illuminate\Support\Collection;

class Plus implements Rock
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

    public function move(Move $move, array $cave): Rock
    {
        $newPositions = array_map(fn (array $row) => array_map(fn (int $x) => $x + $move->direction(), $row), $this->positions);

        $canMove = (new Collection($newPositions))
            ->every(function (array $row, $y) use ($cave) {
                return (new Collection($row))
                    ->every(fn (int $x) => $x > 0 && $x < 8 && ! isset($cave[$y][$x]));
            });

        if (! $canMove) {
            return $this;
        }

        $this->positions = $newPositions;

        return $this;
    }

    public function drop(array $cave): bool
    {
        $newPositions = (new Collection($this->positions))
            ->mapWithKeys(fn (array $row, int $y) => [
                $y - 1 => $row,
            ]);

        $isValid = $newPositions->every(fn (array $row, int $y) => (new Collection($row))
            ->every(fn (int $x) => ! isset($cave[$y][$x]))
        );

        if (! $isValid) {
            return false;
        }

        $this->positions = $newPositions->all();

        return true;
    }

    public function positions(): array
    {
        return (new Collection($this->positions))
            ->flatMap(fn (array $row, int $y) => array_map(fn (int $x) => [$y, $x], $row))
            ->all();
    }
}
