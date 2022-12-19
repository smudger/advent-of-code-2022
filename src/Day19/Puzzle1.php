<?php

namespace Smudger\AdventOfCode2022\Day19;

use Exception;
use Illuminate\Support\Collection;

class Puzzle1
{
    public function __invoke(string $fileName)
    {
        $input = file_get_contents(__DIR__.'/'.$fileName)
            ?: throw new Exception('Failed to read input file.');

        return (new Collection(explode("\n", $input)))
            ->map(function (string $blueprint) {
                preg_match_all('!\d+!', $blueprint, $matches);

                return array_map('intval', $matches[0]);
            })
            ->map(fn (array $blueprint) => new Blueprint(
                id: $blueprint[0],
                recipes: [
                    new Recipe(Robot::Ore, [new MaterialCost(Material::Ore, $blueprint[1])]),
                    new Recipe(Robot::Clay, [new MaterialCost(Material::Ore, $blueprint[2])]),
                    new Recipe(Robot::Obsidian, [new MaterialCost(Material::Ore, $blueprint[3]), new MaterialCost(Material::Clay, $blueprint[4])]),
                    new Recipe(Robot::Geode, [new MaterialCost(Material::Ore, $blueprint[5]), new MaterialCost(Material::Obsidian, $blueprint[6])]),
                ],
            ))
            ->map(fn (Blueprint $blueprint) => $blueprint->determineQualityLevel(minutes: 24))
            ->sum();
    }
}
