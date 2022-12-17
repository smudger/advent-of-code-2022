<?php

namespace Smudger\AdventOfCode2022\Day17;

class Moves
{
    private array $moves;

    private int $currentIndex;

    public function __construct(string $moves)
    {
        $this->moves = str_split($moves);
        $this->currentIndex = 0;
    }

    public function next()
    {
        $index = $this->currentIndex % count($this->moves);
        $this->currentIndex++;

        return match ($this->moves[$index]) {
            '>' => Move::Right,
            '<' => Move::Left,
        };
    }
}
