<?php

namespace Smudger\AdventOfCode2022\Day5;

class Move
{
    public readonly int $times;

    public readonly string $from;

    public readonly string $to;

    public function __construct(string $move)
    {
        [$times, $from, $to] = array_values(array_filter(explode(' ', $move), fn (string $word) => is_numeric($word)));

        $this->times = intval($times);
        $this->from = $from;
        $this->to = $to;
    }
}
