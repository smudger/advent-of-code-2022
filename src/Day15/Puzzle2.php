<?php

namespace Smudger\AdventOfCode2022\Day15;

use Exception;
use Illuminate\Support\Collection;

class Puzzle2
{
    public function __invoke(string $fileName, int $maxCoord = 4000000)
    {
        $input = file_get_contents(__DIR__.'/'.$fileName)
            ?: throw new Exception('Failed to read input file.');
        $sensorsWithDistance = (new Collection(explode("\n", $input)))
            ->map(fn (string $line) => str_replace(['Sensor at x=', ' y=', ' closest beacon is at x='], '', $line))
            ->map(fn (string $line) => explode(':', $line))
            ->map(fn (array $line) => array_map(fn (string $coordinates) => array_map('intval', explode(',', $coordinates)), $line))
            ->map(fn (array $line) => [$line[0], $this->manhattanDistance($line[0], $line[1])])
            ->all();

        $points = [];

        foreach ($sensorsWithDistance as $pair) {
            $point = $pair[0];
            $distance = $pair[1];
            for ($x = $point[0] - ($distance + 1); $x <= $point[0] + ($distance + 1); $x++) {
                $distanceRemaining = ($distance + 1) - abs($x - $point[0]);
                if ($distanceRemaining === 0) {
                    $points[$x][$point[1]] = 1;

                    continue;
                }

                $points[$x][$point[1] + $distanceRemaining] = 1;
                $points[$x][$point[1] - $distanceRemaining] = 1;
            }
        }

        $potentials = (new Collection($points))
            ->map(fn (array $column) => array_keys($column))
            ->map(fn (array $column) => array_filter($column, fn (int $y) => $y >= 0 && $y <= $maxCoord))
            ->filter(fn (array $column, int $x) => $x >= 0 && $x <= $maxCoord)
            ->flatMap(fn (array $column, int $x) => array_map(fn (int $y) => [$x, $y], $column));

        $lostBeacon = $potentials
            ->first(fn (array $position) => (new Collection($sensorsWithDistance))->every(fn (array $pair) => $this->manhattanDistance($position, $pair[0]) > $pair[1]));

        return ($lostBeacon[0] * 4000000) + $lostBeacon[1];
    }

    private function manhattanDistance(array $position1, array $position2): int
    {
        return abs($position2[0] - $position1[0]) + abs($position2[1] - $position1[1]);
    }
}
