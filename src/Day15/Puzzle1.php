<?php

namespace Smudger\AdventOfCode2022\Day15;

use Exception;
use Illuminate\Support\Collection;

class Puzzle1
{
    public function __invoke(string $fileName, int $yLevel = 2000000)
    {
        $input = file_get_contents(__DIR__.'/'.$fileName)
            ?: throw new Exception('Failed to read input file.');
        $coordPairs = (new Collection(explode("\n", $input)))
            ->map(fn (string $line) => str_replace(['Sensor at x=', ' y=', ' closest beacon is at x='], '', $line))
            ->map(fn (string $line) => explode(':', $line))
            ->map(fn (array $line) => array_map(fn (string $coordinates) => array_map('intval', explode(',', $coordinates)), $line));

        $beaconsOnYLevel = $coordPairs
            ->filter(fn (array $pair) => $pair[1][1] === $yLevel)
            ->unique(fn (array $pair) => $pair[1][0]);

        $sensedLocations = $coordPairs
            ->map(fn (array $line) => [$line[0], $this->manhattanDistance($line[0], $line[1])])
            ->flatMap(fn (array $line) => $this->rangeOn($yLevel, ...$line))
            ->unique();

        return $sensedLocations->count() - $beaconsOnYLevel->count();
    }

    private function manhattanDistance(array $position1, array $position2): int
    {
        return abs($position2[0] - $position1[0]) + abs($position2[1] - $position1[1]);
    }

    private function rangeOn(int $yLevel, array $startPosition, int $radius): array
    {
        $verticalDistanceToYLevel = abs($startPosition[1] - $yLevel);
        if ($verticalDistanceToYLevel > $radius) {
            return [];
        }

        $radiusOnYLevel = $radius - $verticalDistanceToYLevel;

        return range($startPosition[0] - $radiusOnYLevel, $startPosition[0] + $radiusOnYLevel);
    }
}
