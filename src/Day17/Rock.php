<?php

namespace Smudger\AdventOfCode2022\Day17;

use Illuminate\Support\Collection;

abstract class Rock
{
    abstract public function spawnAt(int $baseHeight): void;

    abstract public function getPositions(): array;

    abstract public function setPositions(array $positions): void;

    final public function move(Move $move, array $cave): Rock
    {
        $newPositions = array_map(fn (array $row) => array_map(fn (int $x) => $x + $move->direction(), $row), $this->getPositions());

        $canMove = (new Collection($newPositions))
            ->every(function (array $row, $y) use ($cave) {
                return (new Collection($row))
                    ->every(fn (int $x) => $x > 0 && $x < 8 && ! isset($cave[$y][$x]));
            });

        if (! $canMove) {
            return $this;
        }

        $this->setPositions($newPositions);

        return $this;
    }

    final public function drop(array $cave): bool
    {
        $newPositions = (new Collection($this->getPositions()))
            ->mapWithKeys(fn (array $row, int $y) => [
                $y - 1 => $row,
            ]);

        $isValid = $newPositions->every(fn (array $row, int $y) => (new Collection($row))
            ->every(fn (int $x) => ! isset($cave[$y][$x]))
        );

        if (! $isValid) {
            return false;
        }

        $this->setPositions($newPositions->all());

        return true;
    }

    final public function positions(): array
    {
        return (new Collection($this->getPositions()))
            ->flatMap(fn (array $row, int $y) => array_map(fn (int $x) => [$y, $x], $row))
            ->all();
    }
}
