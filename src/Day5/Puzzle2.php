<?php

namespace Smudger\AdventOfCode2022\Day5;

use Exception;
use Illuminate\Support\Collection;

class Puzzle2
{
    public function __invoke(string $fileName): string
    {
        $input = file_get_contents(__DIR__.'/'.$fileName)
            ?: throw new Exception('Failed to read input file.');
        [$crates, $moves] = explode("\n\n", $input);
        $warehouse = new Warehouse($crates);

        $moves = (new Collection(explode("\n", $moves)))
            ->map(fn (string $move) => new Move($move));

        foreach ($moves as $move) {
            $warehouse->moveTogether($move);
        }

        return $warehouse->topCrates();
    }
}
