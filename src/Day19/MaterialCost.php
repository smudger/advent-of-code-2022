<?php

namespace Smudger\AdventOfCode2022\Day19;

class MaterialCost
{
    public function __construct(public readonly Material $material, public readonly int $number)
    {
    }
}
