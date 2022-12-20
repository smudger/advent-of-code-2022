<?php

namespace Smudger\AdventOfCode2022\Day19;

class Blueprint
{
    private int $obsidianForGeode;

    private int $clayForObsidian;

    private int $maxOreRequired;

    /** @param  Recipe[]  $recipes */
    public function __construct(public readonly int $id, public readonly array $recipes)
    {
        $this->obsidianForGeode = array_values(array_filter(array_values(array_filter($this->recipes, fn (Recipe $recipe) => $recipe->robot === Robot::Geode))[0]->materials, fn (MaterialCost $cost) => $cost->material === Material::Obsidian))[0]->number;
        $this->clayForObsidian = array_values(array_filter(array_values(array_filter($this->recipes, fn (Recipe $recipe) => $recipe->robot === Robot::Obsidian))[0]->materials, fn (MaterialCost $cost) => $cost->material === Material::Clay))[0]->number;
        $oreCosts = array_map(fn (Recipe $recipe) => array_values(array_filter($recipe->materials, fn (MaterialCost $cost) => $cost->material === Material::Ore))[0]->number, $this->recipes);
        sort($oreCosts);
        $this->maxOreRequired = end($oreCosts);
    }

    public function determineQualityLevel(int $minutes): int
    {
        $workspaces = [[
            'robots' => [
                Robot::Ore->name => 1,
                Robot::Clay->name => 0,
                Robot::Obsidian->name => 0,
                Robot::Geode->name => 0,
            ],
            'materials' => [
                Material::Ore->name => 1,
                Material::Clay->name => 0,
                Material::Obsidian->name => 0,
                Material::Geode->name => 0,
            ],
            'minute' => 1,
            'previousIndex' => null,
        ]];
        $index = 0;
        $maxGeodes = 0;

        do {
            if ($index % 5000 === 0) {
                $workspacesToConsider = count($workspaces);
                $percentage = round($index / $workspacesToConsider * 100, 1);
                var_dump("Blueprint: $this->id, Most Geodes: $maxGeodes, Workspaces: $index/$workspacesToConsider ($percentage%)");
            }
            [$workspaces, $index, $maxGeodes] = $this->iterateWorkspaces(
                $workspaces,
                $index,
                $minutes,
                $maxGeodes,
            );
        } while ($index < count($workspaces));

        return count($workspaces) === 0 ? 0 : $maxGeodes * $this->id;
    }

    private function iterateWorkspaces(
        array $workspaces,
        int $index,
        int $totalMinutes,
        int $maxGeodes,
    ): array {
        $workspace = $workspaces[$index];

        [$newWorkspaces, $maxGeodes] = $this->getNewWorkspacesForWorkspace($workspace, $totalMinutes, $maxGeodes, $index, $workspaces);

        return [array_merge($workspaces, $newWorkspaces), $index + 1, $maxGeodes];
    }

    private function getNewWorkspacesForWorkspace(array $workspace, int $totalMinutes, int $maxGeodes, int $previousIndex, array $workspaces): array
    {
        $timeRemaining = $totalMinutes - $workspace['minute'];
        $newWorkspaces = [];

        if (($workspace['robots'][Robot::Obsidian->name] * $timeRemaining) + $workspace['materials'][Material::Obsidian->name] < $this->obsidianForGeode * $timeRemaining) {
            // we want an obsidian robot
            $obsidianResult = $this->buildRobot(Robot::Obsidian, $workspace, $timeRemaining, $maxGeodes, $previousIndex);
            if ($obsidianResult !== false) {
                $newWorkspaces[] = $obsidianResult;
            }
        }
        if (($workspace['robots'][Robot::Clay->name] * $timeRemaining) + $workspace['materials'][Material::Clay->name] < $this->clayForObsidian * $timeRemaining) {
            // we want a clay robot
            $clayResult = $this->buildRobot(Robot::Clay, $workspace, $timeRemaining, $maxGeodes, $previousIndex);
            if ($clayResult !== false) {
                $newWorkspaces[] = $clayResult;
            }
        }
        if (($workspace['robots'][Robot::Ore->name] * $timeRemaining) + $workspace['materials'][Material::Ore->name] < $this->maxOreRequired * $timeRemaining) {
            // we want an ore robot
            $oreResult = $this->buildRobot(Robot::Ore, $workspace, $timeRemaining, $maxGeodes, $previousIndex);
            if ($oreResult !== false) {
                $newWorkspaces[] = $oreResult;
            }
        }
        $geodeResult = $this->buildRobot(Robot::Geode, $workspace, $timeRemaining, $maxGeodes, $previousIndex);
        if ($geodeResult !== false) {
            $newWorkspaces[] = $geodeResult;
            if ($geodeResult['materials'][Material::Geode->name] > $maxGeodes) {
                $maxGeodes = $geodeResult['materials'][Material::Geode->name];
            }
        }

        return [$newWorkspaces, $maxGeodes];
    }

    private function buildRobot(Robot $robot, array $workspace, int $timeRemaining, int $maxGeodes, int $previousIndex): array|bool
    {
        $recipe = array_values(array_filter($this->recipes, fn (Recipe $recipe) => $recipe->robot === $robot))[0];

        $materialCosts = array_combine(
            array_map(fn (MaterialCost $materialCost) => $materialCost->material->name, $recipe->materials),
            array_map(fn (MaterialCost $materialCost) => $materialCost->number, $recipe->materials),
        );

        $minutesToGatherResources = array_map(function (MaterialCost $materialCost) use ($workspace) {
            $currentReserve = $workspace['materials'][$materialCost->material->name];
            $generationPerMinute = $workspace['robots'][$materialCost->material->name];

            if ($generationPerMinute === 0) {
                return false;
            }

            return max(intval(ceil(($materialCost->number - $currentReserve) / $generationPerMinute)), 0);
        }, $recipe->materials);

        if (in_array(false, $minutesToGatherResources, true)) {
            return false;
        }

        $minutesToGatherAndProduce = max($minutesToGatherResources) + 1;

        $newWorkspace = [
            'robots' => [
                Robot::Ore->name => $workspace['robots'][Robot::Ore->name] + ($robot === Robot::Ore),
                Robot::Clay->name => $workspace['robots'][Robot::Clay->name] + ($robot === Robot::Clay),
                Robot::Obsidian->name => $workspace['robots'][Robot::Obsidian->name] + ($robot === Robot::Obsidian),
                Robot::Geode->name => $workspace['robots'][Robot::Geode->name] + ($robot === Robot::Geode),
            ],
            'materials' => [
                Material::Ore->name => $workspace['materials'][Material::Ore->name] + ($minutesToGatherAndProduce * $workspace['robots'][Robot::Ore->name]) - ($materialCosts[Material::Ore->name] ?? 0),
                Material::Clay->name => $workspace['materials'][Material::Clay->name] + ($minutesToGatherAndProduce * $workspace['robots'][Robot::Clay->name]) - ($materialCosts[Material::Clay->name] ?? 0),
                Material::Obsidian->name => $workspace['materials'][Material::Obsidian->name] + ($minutesToGatherAndProduce * $workspace['robots'][Robot::Obsidian->name]) - ($materialCosts[Material::Obsidian->name] ?? 0),
                Material::Geode->name => $workspace['materials'][Material::Geode->name] + ($robot === Robot::Geode ? $timeRemaining - $minutesToGatherAndProduce : 0),
            ],
            'minute' => $workspace['minute'] + $minutesToGatherAndProduce,
            'previousIndex' => $previousIndex,
        ];

        if ($minutesToGatherAndProduce >= $timeRemaining) {
            return false;
        }

        $maxGeodesICanProduce = $newWorkspace['materials'][Material::Geode->name]
            + array_sum(range(0, $timeRemaining - $minutesToGatherAndProduce));

        if ($maxGeodesICanProduce < $maxGeodes) {
            return false;
        }

        return $newWorkspace;
    }
}
