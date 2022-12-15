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
        
        $sensedCells = [];

        foreach ($sensorsWithDistance as $pair)
        {
            $sensor = $pair[0];
            $distance = $pair[1];
            $min = $sensor[0] - $distance;
            $max = $sensor[0] + $distance;
            echo "[$min, $max]\n";
            for ($x = max($sensor[0] - $distance, 0); $x <= min($sensor[0] + $distance, $maxCoord); $x++)
            {
                if ($x % 10000 === 0)
                {
                    echo "X: $x\n";
                }
                $distanceRemaining = $distance - abs($sensor[0] - $x);
                for ($y = max($sensor[1] - $distanceRemaining, 0); $y <= min($sensor[1] + $distanceRemaining, $maxCoord); $y++)
                {
                    $sensedCells[$x][$y] = 1;
                }
            }
        }

        $lostBeacon = (new Collection($sensedCells))
            ->filter(fn (array $row) => sizeof($row) < $maxCoord + 1)
            ->map(fn (array $row) => array_values(array_diff(range(0, $maxCoord), array_keys($row)))[0])
            ->all();

        $xCoord = array_values(array_keys($lostBeacon))[0];
        $yCoord = $lostBeacon[$xCoord];
        return ($xCoord * 4000000) + $yCoord;
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
