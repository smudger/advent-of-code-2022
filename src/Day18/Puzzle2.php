<?php

namespace Smudger\AdventOfCode2022\Day18;

use Exception;
use Illuminate\Support\Collection;

class Puzzle2
{
    public function __invoke(string $fileName)
    {
        $input = file_get_contents(__DIR__.'/'.$fileName)
            ?: throw new Exception('Failed to read input file.');
        $points = array_map(fn (string $line) => array_map('intval', explode(',', $line)), explode("\n", $input));
        $shape = [];
        foreach ($points as $point) {
            $shape[$point[0]][$point[1]][$point[2]] = 1;
        }

        $xValues = array_keys($shape);
        sort($xValues);

        $yValues = array_unique(array_merge(...array_map('array_keys', $shape)));
        sort($yValues);

        $zValues = array_unique(array_merge(...array_map(fn (array $middle) => array_merge(...array_map('array_keys', $middle)), $shape)));
        sort($zValues);

        $targetPointOutsideShape = [$xValues[0] - 1, $yValues[0] - 1, $zValues[0] - 1];

        $steps = [[$targetPointOutsideShape, 0]];
        $i = 0;

        do {
            [$i, $steps] = $this->move(
                i: $i,
                steps: $steps,
                minX: $xValues[0] - 1,
                maxX: end($xValues) + 1,
                minY: $yValues[0] - 1,
                maxY: end($yValues) + 1,
                minZ: $zValues[0] - 1,
                maxZ: end($zValues) + 1,
                points: $points,
            );
        } while ($i < count($steps));

        $externalPoints = array_map(fn (array $step) => implode(',', $step[0]), $steps);

        return (new Collection($points))
            ->map(function (array $point) use ($externalPoints) {
                return count(array_intersect([
                    implode(',', [$point[0] + 1, $point[1], $point[2]]),
                    implode(',', [$point[0] - 1, $point[1], $point[2]]),
                    implode(',', [$point[0], $point[1] + 1, $point[2]]),
                    implode(',', [$point[0], $point[1] - 1, $point[2]]),
                    implode(',', [$point[0], $point[1], $point[2] + 1]),
                    implode(',', [$point[0], $point[1], $point[2] - 1]),
                ], $externalPoints));
            })
            ->sum();
    }

    private function move(int $i, array $steps, int $minX, int $maxX, int $minY, int $maxY, int $minZ, int $maxZ, array $points): array
    {
        $step = $steps[$i];
        $position = $step[0];
        $nextSteps = [];

        if ($position[0] > $minX) {
            $nextSteps[] = [[$position[0] - 1, $position[1], $position[2]], $step[1] + 1];
        }

        if ($position[0] < $maxX) {
            $nextSteps[] = [[$position[0] + 1, $position[1], $position[2]], $step[1] + 1];
        }

        if ($position[1] > $minY) {
            $nextSteps[] = [[$position[0], $position[1] - 1, $position[2]], $step[1] + 1];
        }

        if ($position[1] < $maxY) {
            $nextSteps[] = [[$position[0], $position[1] + 1, $position[2]], $step[1] + 1];
        }

        if ($position[2] > $minZ) {
            $nextSteps[] = [[$position[0], $position[1], $position[2] - 1], $step[1] + 1];
        }

        if ($position[2] < $maxZ) {
            $nextSteps[] = [[$position[0], $position[1], $position[2] + 1], $step[1] + 1];
        }

        $stepsToAdd = (new Collection($nextSteps))
            ->filter(fn (array $nextStep) => count(array_filter($steps, fn (array $seenStep) => $seenStep[0] === $nextStep[0])) === 0)
            ->filter(fn (array $nextStep) => ! in_array($nextStep[0], $points))
            ->values()
            ->all();

        return [$i + 1, array_merge($steps, $stepsToAdd)];
    }
}
