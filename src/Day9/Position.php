<?php

namespace Smudger\AdventOfCode2022\Day9;

class Position
{
    public function __construct(
        private int $x,
        private int $y,
    ) {
    }

    public function x(): int
    {
        return $this->x;
    }

    public function y(): int
    {
        return $this->y;
    }

    public function move(string $move): void
    {
        match ($move) {
            'R' => $this->x += 1,
            'L' => $this->x -= 1,
            'U' => $this->y += 1,
            'D' => $this->y -= 1,
        };
    }

    public function __toString()
    {
        return "($this->x,$this->y)";
    }

    public function reconcileWith(Position $other): void
    {
        if ($this->equals($other) || $this->distance($other) === 1) {
            return;
        }

        $difference = $other->minus($this);
        $this->x += ($difference->x > 0) - ($difference->x < 0);
        $this->y += ($difference->y > 0) - ($difference->y < 0);
    }

    public function equals(Position $other): bool
    {
        return $this->x === $other->x
            && $this->y === $other->y;
    }

    public function clone(): Position
    {
        return new Position($this->x, $this->y);
    }

    private function distance(Position $other): int
    {
        return max(abs($other->x - $this->x), abs($other->y - $this->y));
    }

    private function minus(Position $other): Position
    {
        return new Position($this->x - $other->x, $this->y - $other->y);
    }
}
