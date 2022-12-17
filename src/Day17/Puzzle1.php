<?php

namespace Smudger\AdventOfCode2022\Day17;

class Puzzle1
{
    public function __invoke(string $fileName)
    {
        return (new CaveHeight())($fileName, 2022);
    }
}
