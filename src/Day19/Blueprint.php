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

        while ($workspaces[0]['minute'] < $minutes) {
            $minute = $workspaces[0]['minute'];
            $workspaceCount = count($workspaces);
            var_dump("Blueprint: $this->id, Minute: $minute, Workspaces To Consider: $workspaceCount");
            $workspaces = array_values($this->iterateWorkspaces(
                $workspaces,
                $minutes,
                oreOverProductionTolerance: 100,
                clayOverProductionTolerance: 20,
                obsidianOverProductionTolerance: 15,
            ));
        }

        $temp = (new Collection($workspaces))
            ->map(fn (array $workspace) => $workspace['materials'][Material::Geode->name])
            ->sortDesc()
            ->first();
        var_dump($temp);

        return $temp;
    }

    private function iterateWorkspaces(
        array $workspaces,
        int $totalMinutes,
        float $oreOverProductionTolerance,
        float $clayOverProductionTolerance,
        float $obsidianOverProductionTolerance,
    ): array {
        $workspacesAfterIteration = [];
        foreach ($workspaces as $workspace) {
            if ($workspace['minute'] >= 20 && $workspace['materials'][Material::Geode->name] === 0) {
                continue;
            }
            $maxOreUsed = $this->maxOreRequired * ($totalMinutes - $workspace['minute']);
            if (floatval($workspace['materials'][Material::Ore->name]) > $maxOreUsed * $oreOverProductionTolerance) {
                continue;
            }
            $maxClayUsed = $this->clayForObsidian * ($totalMinutes - $workspace['minute']);
            if (floatval($workspace['materials'][Material::Clay->name]) > $maxClayUsed * $clayOverProductionTolerance) {
                continue;
            }
            $maxObsidianUsed = $this->obsidianForGeode * ($totalMinutes - $workspace['minute']);
            if (floatval($workspace['materials'][Material::Obsidian->name]) > $maxObsidianUsed * $obsidianOverProductionTolerance) {
                continue;
            }
            if ($workspace['minute'] > 10 && array_sum($workspace['robots']) < (0.4 * $workspace['minute'])) {
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

            var_dump($noProductionWorkspace['materials']);

            $satisifedRecipes = $this->getSatisfiedRecipesForWorkspace($workspace);

            foreach ($satisifedRecipes as $satisifedRecipe) {
                if (in_array($satisifedRecipe->robot->name, $noProductionWorkspace['maxedOutRobots'])) {
                    continue;
                }
                if ($satisifedRecipe->robot === Robot::Obsidian) {
                    if (($workspace['robots'][Robot::Obsidian->name] === $this->obsidianForGeode - 1)) {
                        $noProductionWorkspace['maxedOutRobots'][] = Robot::Obsidian->name;
                    }
                }
                if ($satisifedRecipe->robot === Robot::Clay) {
                    if (($workspace['robots'][Robot::Clay->name] === $this->clayForObsidian - 1)) {
                        $noProductionWorkspace['maxedOutRobots'][] = Robot::Clay->name;
                    }
                }
                if ($satisifedRecipe->robot === Robot::Ore) {
                    if (($workspace['robots'][Robot::Ore->name] === $this->maxOreRequired - 1)) {
                        $noProductionWorkspace['maxedOutRobots'][] = Robot::Ore->name;
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

        return $workspacesAfterIteration[0]['minute'] > 12
            ? array_filter($workspacesAfterIteration, fn (int $key) => $key % 7 < 3, ARRAY_FILTER_USE_KEY)
            : $workspacesAfterIteration;
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
