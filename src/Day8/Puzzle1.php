<?php

namespace Smudger\AdventOfCode2022\Day8;

use Exception;
use Illuminate\Support\Collection;

class Puzzle1
{
    public function __invoke(string $fileName)
    {
        $input = file_get_contents(__DIR__.'/'.$fileName)
            ?: throw new Exception('Failed to read input file.');
        $stringTrees = array_map(fn ($row) => str_split($row), explode("\n", $input));
        $trees = array_map(fn ($row) => array_map('intval', $row), $stringTrees);

        $visibleCount = 0;

        foreach ($trees as $i => $row) {
            foreach ($row as $j => $tree) {
                $left = array_slice($row, 0, $j);
                $right = $j + 1 === count($row) ? [] : array_slice($row, $j + 1);
                $column = array_map(fn ($row) => $row[$j], $trees);
                $up = array_slice($column, 0, $i);
                $down = $i + 1 === count($column) ? [] : array_slice($column, $i + 1);

                $directions = [$left, $right, $up, $down];

                $isVisible = (new Collection($directions))
                    ->filter(function (array $direction) use ($tree) {
                        return (new Collection($direction))
                            ->every(fn (int $otherTree) => $otherTree < $tree);
                    })
                    ->isNotEmpty();

                if ($isVisible) {
                    $visibleCount++;
                }
            }
        }

        return $visibleCount;
    }
}
