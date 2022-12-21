<?php

namespace Smudger\AdventOfCode2022\Day21;

use Exception;

class Puzzle2
{
    public function __invoke(string $fileName)
    {
        $input = file_get_contents(__DIR__.'/'.$fileName)
            ?: throw new Exception('Failed to read input file.');
        // TODO: Solve puzzle 2.
    }
}
