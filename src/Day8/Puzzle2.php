<?php

namespace Smudger\AdventOfCode2022\Day8;

use Exception;
use Illuminate\Support\Collection;

class Puzzle2
{
    public function __invoke(string $fileName)
    {
        $input = file_get_contents(__DIR__.'/'.$fileName)
            ?: throw new Exception('Failed to read input file.');
        $stringTrees = array_map(fn ($row) => str_split($row), explode("\n", $input));
        $trees = array_map(fn ($row) => array_map('intval', $row), $stringTrees);

        $scores = [];

        foreach ($trees as $i => $row) {
            foreach ($row as $j => $tree) {
                $left = array_slice($row, 0, $j);
                $right = $j + 1 === count($row) ? [] : array_slice($row, $j + 1);
                $column = array_map(fn ($row) => $row[$j], $trees);
                $up = array_slice($column, 0, $i);
                $down = $i + 1 === count($column) ? [] : array_slice($column, $i + 1);

                $directions = [array_reverse($left), $right, array_reverse($up), $down];

                $scores[] = (new Collection($directions))
                    ->map(function (array $direction) use ($tree) {
                        $blockingTreeIndex = (new Collection($direction))->search(fn ($otherTree) => $otherTree >= $tree);
                        if ($blockingTreeIndex === false) {
                            return count($direction);
                        }

                        return $blockingTreeIndex + 1;
                    })
                    ->reduce(fn ($carry, $treeCount) => $carry * $treeCount, 1);
            }
        }

        sort($scores);

        return array_reverse($scores)[0];
    }
}
