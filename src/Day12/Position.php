<?php

namespace Smudger\AdventOfCode2022\Day12;

class Position
{
    public function __construct(
        public readonly int $x,
        public readonly int $y,
    ) {
    }

    public function __toString()
    {
        return "($this->x,$this->y)";
    }

    public function equals(Position $other): bool
    {
        return $this->x === $other->x
            && $this->y === $other->y;
    }
}
