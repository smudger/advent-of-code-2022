<?php

namespace Smudger\AdventOfCode2022\Day14;

use Exception;

class Puzzle1
{
    public function __invoke(string $fileName)
    {
        $input = file_get_contents(__DIR__.'/'.$fileName)
            ?: throw new Exception('Failed to read input file.');
        $cave = new Cave($input);

        while (true) {
            $endPosition = $cave->moveSand(new Position(500, 0));
            if ($endPosition === false) {
                break;
            }
            $cave->addSand($endPosition);
        }

        return $cave->sandCount();
    }
}
