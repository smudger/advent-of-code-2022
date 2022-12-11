<?php

namespace Smudger\AdventOfCode2022\Day11;

use Exception;
use Illuminate\Support\Collection;

class Puzzle1
{
    public function __invoke(string $fileName)
    {
        $input = file_get_contents(__DIR__.'/'.$fileName)
            ?: throw new Exception('Failed to read input file.');
        $monkeys = new Collection(array_map(fn (string $definition) => new Monkey($definition), explode("\n\n", $input)));

        (new Collection(range(1, 20)))->each(function () use ($monkeys) {
            $monkeys->each(function (Monkey $monkey) use ($monkeys) {
                $itemsToPass = $monkey->takeTurn(fn (int $item) => intval(floor($item / 3)));
                foreach ($itemsToPass as $receiver => $items) {
                    $monkeys
                        ->first(fn (Monkey $monkey) => $monkey->id() === strval($receiver))
                        ->receive($items);
                }
            });
        });

        return $monkeys
            ->map(fn (Monkey $monkey) => $monkey->inspectionCount())
            ->sortDesc()
            ->take(2)
            ->reduce(fn (int $carry, int $inspectionCount) => $carry * $inspectionCount, 1);
    }
}
