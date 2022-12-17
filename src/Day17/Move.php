<?php

namespace Smudger\AdventOfCode2022\Day17;

enum Move
{
    case Left;
    case Right;

    public function direction(): int
    {
        return match ($this) {
            self::Left => -1,
            self::Right => 1,
        };
    }
}
