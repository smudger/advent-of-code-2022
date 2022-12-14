<?php

namespace Smudger\AdventOfCode2022\Day14;

use Exception;

class Position
{
    public function __construct(
        public readonly int $x,
        public readonly int $y,
    ) {
    }

    /** @return Position[] */
    public function lineTo(Position $other): array
    {
        if ($this->x === $other->x) {
            return array_map(fn (int $y) => new Position($this->x, $y), range($this->y, $other->y));
        }

        if ($this->y === $other->y) {
            return array_map(fn (int $x) => new Position($x, $this->y), range($this->x, $other->x));
        }

        throw new Exception('These positions do not lie on the same horizontal or vertical line');
    }

    public function equals(Position $other): bool
    {
        return $this->x === $other->x
            && $this->y === $other->y;
    }

    public function down(): Position
    {
        return new Position($this->x, $this->y + 1);
    }

    public function downLeft(): Position
    {
        return new Position($this->x - 1, $this->y + 1);
    }

    public function downRight(): Position
    {
        return new Position($this->x + 1, $this->y + 1);
    }
}
