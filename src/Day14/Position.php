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
}
