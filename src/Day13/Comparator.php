<?php

namespace Smudger\AdventOfCode2022\Day13;

class Comparator
{
    public function compare($left, $right): int
    {
        if (is_null($left)) {
            return -1;
        }

        if (is_null($right)) {
            return 1;
        }

        if (is_int($left) && is_int($right)) {
            return ($left > $right) - ($left < $right);
        }

        if (is_int($left) && is_array($right)) {
            return self::compare([$left], $right);
        }

        if (is_array($left) && is_int($right)) {
            return self::compare($left, [$right]);
        }

        $zipped = array_map(null, $left, $right);
        $comparison = array_map(fn (array $pair) => self::compare($pair[0], $pair[1]), $zipped);

        $differences = array_values(array_filter($comparison));

        return empty($differences)
            ? 0
            : $differences[0];
    }
}
