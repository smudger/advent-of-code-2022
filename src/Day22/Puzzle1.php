<?php

namespace Smudger\AdventOfCode2022\Day22;

use Exception;

class Puzzle1
{
    public function __invoke(string $fileName)
    {
        $input = file_get_contents(__DIR__.'/'.$fileName)
            ?: throw new Exception('Failed to read input file.');
        [$grid, $rawInstructions] = explode("\n\n", $input);
        $x = strpos(explode("\n", $grid)[0], '.') + 1;

        $instructions = array_map(
            fn (string $instruction) => is_numeric($instruction) ? intval($instruction) : $instruction,
            preg_split("/\s+/", preg_replace('/([A-Z])/', ' $1 ', $rawInstructions))
        );

        $grid = new Grid($grid);

        $position = [1, $x, 0];

        foreach ($instructions as $instruction) {
            $position = $grid->followInstruction($position, $instruction);
        }

        return (1000 * $position[0])
            + (4 * $position[1])
            + $position[2];
    }
}
