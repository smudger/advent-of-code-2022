<?php

namespace Smudger\AdventOfCode2022\Day23;

use Exception;
use Illuminate\Support\Collection;

class Puzzle1
{
    public function __invoke(string $fileName)
    {
        $input = file_get_contents(__DIR__.'/'.$fileName)
            ?: throw new Exception('Failed to read input file.');
        $elves = (new Collection(array_map('str_split', explode("\n", $input))))
            ->map(fn (array $row) => array_filter($row, fn (string $position) => $position === '#'))
            ->filter()
            ->all();

        $round = 1;
        $directions = [
            // North
            [
                'move' => [-1, 0],
                'checks' => [
                    [-1, -1],
                    [-1, 0],
                    [-1, 1],
                ],
            ],
            // South
            [
                'move' => [1, 0],
                'checks' => [
                    [1, -1],
                    [1, 0],
                    [1, 1],
                ],
            ],
            // West
            [
                'move' => [0, -1],
                'checks' => [
                    [-1, -1],
                    [0, -1],
                    [1, -1],
                ],
            ],
            // East
            [
                'move' => [0, 1],
                'checks' => [
                    [-1, 1],
                    [0, 1],
                    [1, 1],
                ],
            ],
        ];

        while ($round <= 10) {
            // do round
            $considerations = $this->considerMoves($elves, $directions);
            if (count(array_filter($considerations, fn (array $consideration) => $consideration['isSatisfied'])) === count($considerations)) {
                break;
            }
            $firstDirection = array_shift($directions);
            array_push($directions, $firstDirection);
            $elves = $this->move($considerations);
//            var_dump("=== Round $round ===");
//            var_dump($elves);
            $round++;
        }

        $yValues = array_keys($elves);
        sort($yValues);
        $height = 1 + end($yValues) - $yValues[0];
        $xBounds = array_reduce($elves, function (array $carry, array $row) {
            $xValues = array_keys($row);
            sort($xValues);
            if (is_null($carry[0]) || $xValues[0] < $carry[0]) {
                $carry[0] = $xValues[0];
            }
            if (is_null($carry[1]) || end($xValues) > $carry[1]) {
                $carry[1] = end($xValues);
            }

            return $carry;
        }, [null, null]);
        $width = 1 + $xBounds[1] - $xBounds[0];
        $numberOfElves = array_reduce($elves, fn (int $carry, array $row) => $carry + count($row), 0);

        return ($width * $height) - $numberOfElves;
    }

    private function considerMoves(array $elves, array $directions): array
    {
        return array_merge(...array_map(function (array $row, int $y) use ($directions, $elves) {
            return array_map(function (string $_, int $x) use ($directions, $elves, $y) {
                $surroundings = array_map(fn (array $point) => [$y + $point[0], $x + $point[1]], [
                    [-1, 0], // N
                    [-1, 1], // NE
                    [0, 1], // E
                    [1, 1], // SE
                    [1, 0], // S
                    [1, -1], // SW
                    [0, -1], // W
                    [-1, -1], // NW
                ]);
                $occupiedSurroundings = array_filter($surroundings, fn (array $point) => isset($elves[$point[0]][$point[1]]));
                if (count($occupiedSurroundings) === 0) {
                    return ['old' => [$y, $x], 'new' => [$y, $x], 'isSatisfied' => true];
                }

                foreach ($directions as $directionToConsider) {
                    $pointsToConsider = array_map(fn (array $point) => [$y + $point[0], $x + $point[1]], $directionToConsider['checks']);
                    $occupiedPoints = array_filter($pointsToConsider, fn (array $point) => isset($elves[$point[0]][$point[1]]));
                    if (count($occupiedPoints) === 0) {
                        return ['old' => [$y, $x], 'new' => [$y + $directionToConsider['move'][0], $x + $directionToConsider['move'][1]], 'isSatisfied' => false];
                    }
                }

                return ['old' => [$y, $x], 'new' => [$y, $x], 'isSatisfied' => false];
            }, $row, array_keys($row));
        }, $elves, array_keys($elves)));
    }

    private function move(array $considerations): array
    {
        $duplicates = array_keys(array_filter(array_count_values(array_map(fn (array $consideration) => implode(', ', $consideration['new']), $considerations)), fn (int $count) => $count > 1));

        $result = [];

        foreach ($considerations as $consideration) {
            if (in_array(implode(', ', $consideration['new']), $duplicates)) {
                $result[$consideration['old'][0]][$consideration['old'][1]] = '#';
            } else {
                $result[$consideration['new'][0]][$consideration['new'][1]] = '#';
            }
        }

        return $result;
    }
}
