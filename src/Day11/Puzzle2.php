<?php

namespace Smudger\AdventOfCode2022\Day11;

use Exception;
use Illuminate\Support\Collection;

class Puzzle2
{
    public function __invoke(string $fileName)
    {
        $input = file_get_contents(__DIR__.'/'.$fileName)
            ?: throw new Exception('Failed to read input file.');
        $monkeys = new Collection(array_map(fn (string $definition) => new Monkey($definition), explode("\n\n", $input)));

        $commonMultiple = $monkeys
            ->reduce(fn (int $carry, Monkey $monkey) => $carry * $monkey->divisor(), 1);

        (new Collection(range(1, 10000)))->each(function () use ($monkeys, $commonMultiple) {
            $monkeys->each(function (Monkey $monkey) use ($monkeys, $commonMultiple) {
                $itemsToPass = $monkey->takeTurn(fn (int $item) => $item % $commonMultiple);
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
