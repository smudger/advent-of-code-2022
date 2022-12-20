<?php

namespace Smudger\AdventOfCode2022\Day19;

use Illuminate\Support\Collection;

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
                Material::Ore->name => 0,
                Material::Clay->name => 0,
                Material::Obsidian->name => 0,
                Material::Geode->name => 0,
            ],
            'minute' => 0,
            'maxedOutRobots' => [],
        ]];
        $mostGeodesSeen = 0;

        while (isset($workspaces[0]['minute']) && $workspaces[0]['minute'] < $minutes) {
            $minute = $workspaces[0]['minute'];
            $workspaceCount = count($workspaces);
            var_dump("Blueprint: $this->id, Minute: $minute, Workspaces To Consider: $workspaceCount, Most Geodes Seen: $mostGeodesSeen");
            [$workspaces, $mostGeodesSeen] = array_values($this->iterateWorkspaces(
                $workspaces,
                $minutes,
                $mostGeodesSeen,
            ));
        }

        if (count($workspaces) === 0) {
            return 0;
        }

        return (new Collection($workspaces))
            ->map(fn (array $workspace) => $workspace['materials'][Material::Geode->name])
            ->sortDesc()
            ->first() * $this->id;
    }

    private function iterateWorkspaces(
        array $workspaces,
        int $totalMinutes,
        int $mostGeodesSeen,
    ): array {
        $workspacesAfterIteration = [];
        $timeRemaining = $totalMinutes - $workspaces[0]['minute'];
        foreach ($workspaces as $workspace) {
            $geodesIHaveProduced = $workspace['materials'][Material::Geode->name];
            $myGeodeRobots = $workspace['robots'][Robot::Geode->name];
            $geodesICouldProduce = array_sum(range($myGeodeRobots, ($myGeodeRobots + $timeRemaining) - 1));
            $maxGeodesICouldProduce = $geodesIHaveProduced + $geodesICouldProduce;
            $maxGeodesCouldBeProduced = $mostGeodesSeen + $timeRemaining;
            if ($maxGeodesICouldProduce < $maxGeodesCouldBeProduced) {
                var_dump("Too Few Geodes: (Could Make: $maxGeodesICouldProduce, Needed: $maxGeodesCouldBeProduced)");

                continue;
            }

            $noProductionWorkspace = [
                'robots' => $workspace['robots'],
                'materials' => [
                    Material::Ore->name => $workspace['materials'][Material::Ore->name] + $workspace['robots'][Robot::Ore->name],
                    Material::Clay->name => $workspace['materials'][Material::Clay->name] + $workspace['robots'][Robot::Clay->name],
                    Material::Obsidian->name => $workspace['materials'][Material::Obsidian->name] + $workspace['robots'][Robot::Obsidian->name],
                    Material::Geode->name => $workspace['materials'][Material::Geode->name] + $workspace['robots'][Robot::Geode->name],
                ],
                'minute' => $workspace['minute'] + 1,
                'maxedOutRobots' => $workspace['maxedOutRobots'],
            ];

            if ($noProductionWorkspace['materials'][Material::Geode->name] > $mostGeodesSeen) {
                $mostGeodesSeen = $workspace['materials'][Material::Geode->name];
            }

            $satisifedRecipes = $this->getSatisfiedRecipesForWorkspace($workspace);

            foreach ($satisifedRecipes as $satisifedRecipe) {
                if (in_array($satisifedRecipe->robot->name, $noProductionWorkspace['maxedOutRobots'])) {
                    continue;
                }
                if ($satisifedRecipe->robot === Robot::Obsidian) {
                    if (($workspace['robots'][Robot::Obsidian->name] * $timeRemaining) + $workspace['materials'][Material::Obsidian->name] >= $this->obsidianForGeode * $timeRemaining) {
                        $noProductionWorkspace['maxedOutRobots'][] = Robot::Obsidian->name;

                        continue;
                    }
                }
                if ($satisifedRecipe->robot === Robot::Clay) {
                    if (($workspace['robots'][Robot::Clay->name] * $timeRemaining) + $workspace['materials'][Material::Clay->name] >= $this->clayForObsidian * $timeRemaining) {
                        $noProductionWorkspace['maxedOutRobots'][] = Robot::Clay->name;

                        continue;
                    }
                }
                if ($satisifedRecipe->robot === Robot::Ore) {
                    if (($workspace['robots'][Robot::Ore->name] * $timeRemaining) + $workspace['materials'][Material::Ore->name] >= $this->maxOreRequired * $timeRemaining) {
                        $noProductionWorkspace['maxedOutRobots'][] = Robot::Ore->name;

                        continue;
                    }
                }

                $materialCosts = (new Collection($satisifedRecipe->materials))->mapWithKeys(fn (MaterialCost $materialCost) => [$materialCost->material->name => $materialCost->number])->all();
                $workspacesAfterIteration[] = [
                    'robots' => [
                        Robot::Ore->name => $satisifedRecipe->robot === Robot::Ore ? $workspace['robots'][Robot::Ore->name] + 1 : $workspace['robots'][Robot::Ore->name],
                        Robot::Clay->name => $satisifedRecipe->robot === Robot::Clay ? $workspace['robots'][Robot::Clay->name] + 1 : $workspace['robots'][Robot::Clay->name],
                        Robot::Obsidian->name => $satisifedRecipe->robot === Robot::Obsidian ? $workspace['robots'][Robot::Obsidian->name] + 1 : $workspace['robots'][Robot::Obsidian->name],
                        Robot::Geode->name => $satisifedRecipe->robot === Robot::Geode ? $workspace['robots'][Robot::Geode->name] + 1 : $workspace['robots'][Robot::Geode->name],
                    ],
                    'materials' => [
                        Material::Ore->name => $noProductionWorkspace['materials'][Material::Ore->name] - ($materialCosts[Material::Ore->name] ?? 0),
                        Material::Clay->name => $noProductionWorkspace['materials'][Material::Clay->name] - ($materialCosts[Material::Clay->name] ?? 0),
                        Material::Obsidian->name => $noProductionWorkspace['materials'][Material::Obsidian->name] - ($materialCosts[Material::Obsidian->name] ?? 0),
                        Material::Geode->name => $noProductionWorkspace['materials'][Material::Geode->name],
                    ],
                    'minute' => $workspace['minute'] + 1,
                    'maxedOutRobots' => $noProductionWorkspace['maxedOutRobots'],
                ];
            }

            $couldMakeAGeodeRobot = count(array_filter($satisifedRecipes, fn (Recipe $recipe) => $recipe->robot === Robot::Geode)) > 0;
            if (count($satisifedRecipes) < 4) {
                if (! ($couldMakeAGeodeRobot && count($noProductionWorkspace['maxedOutRobots']) === 3)) {
                    $workspacesAfterIteration[] = $noProductionWorkspace;
                }
            }
        }

        return [$workspacesAfterIteration, $mostGeodesSeen];
    }

    /** @return Recipe[] */
    private function getSatisfiedRecipesForWorkspace(array $workspace): array
    {
        $availableMaterials = $workspace['materials'];

        return array_filter($this->recipes, function (Recipe $recipe) use ($availableMaterials) {
            $materialCosts = $recipe->materials;
            $unsatisfiedCosts = array_filter($materialCosts, fn (MaterialCost $cost) => $cost->number > $availableMaterials[$cost->material->name]);

            return count($unsatisfiedCosts) === 0;
        });
    }
}
