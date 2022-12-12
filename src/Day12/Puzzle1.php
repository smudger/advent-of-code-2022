<?php

namespace Smudger\AdventOfCode2022\Day12;

use Exception;
use Illuminate\Support\Collection;

class Puzzle1
{
    public function __invoke(string $fileName)
    {
        $input = file_get_contents(__DIR__.'/'.$fileName)
            ?: throw new Exception('Failed to read input file.');
        $grid = new Grid($input);
        $path = $grid->findPathFromStartToEnd();

        return (new Collection($path))
            ->first(fn (Step $step) => $step->position->equals($grid->start))
            ->count;
    }
}
