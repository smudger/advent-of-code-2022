<?php

namespace Smudger\AdventOfCode2022\Day17;

class Puzzle2
{
    public function __invoke(string $fileName)
    {
        return (new CaveHeight())($fileName, 1000000000000);
    }
}
