<?php

namespace Smudger\AdventOfCode2022\Day14;

use Exception;

class Puzzle2
{
    public function __invoke(string $fileName)
    {
        $input = file_get_contents(__DIR__.'/'.$fileName)
            ?: throw new Exception('Failed to read input file.');
        $cave = new EfficientCave($input);

        while (true) {
            $endPosition = $cave->moveSandWithFloor([500, 0]);
            $cave->addSand($endPosition);
            if ($endPosition === [500, 0]) {
                break;
            }
        }

        return $cave->sandCount();
    }
}
