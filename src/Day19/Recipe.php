<?php

namespace Smudger\AdventOfCode2022\Day19;

class Recipe
{
    public function __construct(public readonly Robot $robot, public readonly array $materials)
    {
    }
}
