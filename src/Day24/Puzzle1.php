<?php

namespace Smudger\AdventOfCode2022\Day24;

use Exception;

class Puzzle1
{
    private int $rightToLeftVanishPoint;

    private int $rightToLeftAppearPoint;

    private int $leftToRightVanishPoint;

    private int $leftToRightAppearPoint;

    private int $bottomToTopVanishPoint;

    private int $bottomToTopAppearPoint;

    private int $topToBottomVanishPoint;

    private int $topToBottomAppearPoint;

    private int $width;

    private int $height;

    public function __invoke(string $fileName)
    {
        $input = file_get_contents(__DIR__.'/'.$fileName)
            ?: throw new Exception('Failed to read input file.');
        $initialField = array_map(fn (string $row) => array_map(fn (string $char) => $char === '.' ? [] : [$char], str_split($row)), explode("\n", $input));
        $start = [0, array_search([], $initialField[0])];
        $end = [count($initialField) - 1, array_search([], end($initialField))];

        $this->width = count($initialField[0]);
        $this->height = count($initialField);
        $this->rightToLeftVanishPoint = count($initialField[0]) - 1;
        $this->rightToLeftAppearPoint = 1;
        $this->leftToRightVanishPoint = 0;
        $this->leftToRightAppearPoint = count($initialField[0]) - 2;
        $this->bottomToTopVanishPoint = count($initialField) - 1;
        $this->bottomToTopAppearPoint = 1;
        $this->topToBottomVanishPoint = 0;
        $this->topToBottomAppearPoint = count($initialField) - 2;

        $fields = [$initialField];
        $field = $initialField;

        while (true) {
            $newField = $this->iterateField($field);
            if ($newField === $initialField) {
                break;
            }
            $fields[] = $newField;
            $field = $newField;
        }

        $steps = [[...$start, 0]];
        $index = 0;

        while (count(array_filter($steps, fn (array $step) => $step[0] === $end[0] && $step[1] === $end[1])) === 0) {
            $steps = $this->iterateSteps($steps, $index, $fields);
            $index++;
        }

        return array_values(array_filter($steps, fn (array $step) => $step[0] === $end[0] && $step[1] === $end[1]))[0][2];
    }

    private function iterateField(array $field): array
    {
        $row = array_fill(0, $this->width, []);
        $result = array_fill(0, $this->height, $row);
        foreach ($field as $y => $row) {
            foreach ($row as $x => $blizzards) {
                if ($blizzards === ['#']) {
                    $result[$y][$x] = ['#'];

                    continue;
                }

                if ($blizzards === []) {
                    continue;
                }

                foreach ($blizzards as $blizzard) {
                    $newPosition = match ($blizzard) {
                        '>' => [$y, $x + 1 === $this->rightToLeftVanishPoint ? $this->rightToLeftAppearPoint : $x + 1],
                        'v' => [$y + 1 === $this->bottomToTopVanishPoint ? $this->bottomToTopAppearPoint : $y + 1, $x],
                        '<' => [$y, $x - 1 === $this->leftToRightVanishPoint ? $this->leftToRightAppearPoint : $x - 1],
                        '^' => [$y - 1 === $this->topToBottomVanishPoint ? $this->topToBottomAppearPoint : $y - 1, $x],
                    };

                    $result[$newPosition[0]][$newPosition[1]][] = $blizzard;
                }
            }
        }

        return $result;
    }

    private function iterateSteps(array $steps, int $index, array $fields): array
    {
        $step = $steps[$index];

        $potentialSteps = [
            [$step[0], $step[1], $step[2] + 1],
            [$step[0] + 1, $step[1], $step[2] + 1],
            [$step[0] - 1, $step[1], $step[2] + 1],
            [$step[0], $step[1] + 1, $step[2] + 1],
            [$step[0], $step[1] - 1, $step[2] + 1],
        ];

        $field = $fields[($step[2] + 1) % count($fields)];
        $validSteps = array_filter($potentialSteps, function (array $step) use ($field, $fields, $steps) {
            if (! isset($field[$step[0]][$step[1]])) {
                return false;
            }

            if (count(array_filter($steps, fn (array $seenStep) => $seenStep[0] === $step[0] && $seenStep[1] === $step[1] && ($seenStep[2] - $step[2]) % count($fields) === 0)) > 0) {
                return false;
            }

            return count($field[$step[0]][$step[1]]) === 0;
        });

        return array_merge($steps, $validSteps);
    }
}
