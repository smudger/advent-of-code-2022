<?php

namespace Smudger\AdventOfCode2022\Day17;

class Rocks
{
    private const ROCKS = [
        Horizontal::class,
        Plus::class,
        Letter::class,
        Vertical::class,
        Square::class,
    ];

    public function get(int $i): Rock
    {
        $index = ($i - 1) % count(self::ROCKS);
        $class = self::ROCKS[$index];

        return new $class();
    }
}
