<?php

namespace Smudger\AdventOfCode2022\Day16;

use Exception;
use Illuminate\Support\Collection;

class Puzzle2
{
    public function __invoke(string $fileName, int $split = 7)
    {
        $input = file_get_contents(__DIR__.'/'.$fileName)
            ?: throw new Exception('Failed to read input file.');
        $valves = (new Collection(explode("\n", $input)))
            ->map(fn (string $line) => str_replace(['Valve ', ' has flow rate', ' tunnels lead to valves ', ' tunnel leads to valve '], '', $line))
            ->map(fn (string $line) => explode(';', $line))
            ->map(fn (array $line) => [explode('=', $line[0])[0], intval(explode('=', $line[0])[1]), explode(', ', $line[1])])
            ->mapWithKeys(fn (array $valve) => [$valve[0] => $valve]);

        $distances = $valves
            ->mapWithKeys(fn (array $valve) => [
                $valve[0] => $this->distances($valve[0], $valves->all()),
            ]);

        $distancesWorthVisiting = $distances
            ->map(fn (array $distances) => (new Collection($distances))
                ->filter(fn (int $distance, string $valve) => $valves->get($valve)[1] > 0)
                ->all()
            )
            ->all();

        $interestingValves = $valves->filter(fn (array $valve) => $valve[1] > 0)->all();

        $powerSet = [[]];

        foreach ($interestingValves as $valve) {
            foreach ($powerSet as $combination) {
                $powerSet[] = array_merge([$valve[0]], $combination);
            }
        }

        return (new Collection($powerSet))
            ->filter(fn (array $set) => count($set) === $split)
            ->map(fn (array $mine) => [$mine, array_diff(array_keys($interestingValves), $mine)])
            ->map(function (array $pair) use ($distancesWorthVisiting, $valves) {
                $valvesForMe = $pair[0];
                $valvesForElephant = $pair[1];

                return $this->checkWorkSplit($valvesForMe, $valvesForElephant, $distancesWorthVisiting, $valves);
            })
            ->sortDesc()
            ->first();
    }

    private function distances(string $valve, array $valves): array
    {
        $steps = [[$valve, 0]];
        $i = 0;

        do {
            [$i, $steps] = $this->move($i, $steps, $valves);
            $seenValves = (new Collection($steps))
                ->unique(fn (array $step) => $step[0])
                ->values()
                ->all();
        } while (count($seenValves) < count($valves));

        return (new Collection($steps))
            ->mapWithKeys(fn (array $step) => [$step[0] => $step[1]])
            ->all();
    }

    private function move(int $i, array $steps, array $valves): array
    {
        $nextSteps = $valves[$steps[$i][0]][2];

        $stepsToAdd = (new Collection($nextSteps))
            ->filter(fn (string $nextStep) => count(array_filter($steps, fn (array $seenStep) => $seenStep[0] === $nextStep)) === 0)
            ->map(fn (string $nextStep) => [$nextStep, $steps[$i][1] + 1])
            ->values()
            ->all();

        return [$i + 1, array_merge($steps, $stepsToAdd)];
    }

    private function openValves(int $i, array $steps, array $distancesWorthVisiting, Collection $valves, array $valvesForMe)
    {
        $step = $steps[$i];

        $nextValves = (new Collection($distancesWorthVisiting[$step[0]]))
            ->filter(fn (int $distance, string $valve) => $step[1] - ($distance + 1) > 0)
            ->filter(fn (int $distance, string $valve) => ! isset($step[2][$valve]))
            ->filter(fn (int $distance, string $valve) => in_array($valve, $valvesForMe))
            ->map(function (int $distance, string $valve) use ($step, $valves) {
                $timeRemaining = $step[1] - ($distance + 1);
                $openedValves = $step[2];
                $openedValves[$valve] = $timeRemaining * $valves->get($valve)[1];

                return [$valve, $timeRemaining, $openedValves];
            })
            ->values()
            ->all();

        return [$i + 1, array_merge($steps, $nextValves)];
    }

    private function checkWorkSplit(array $valvesForMe, array $valvesForElephant, array $distancesWorthVisiting, Collection $valves): int
    {
        $mySteps = [['AA', 26, []]];
        $i = 0;

        do {
            [$i, $mySteps] = $this->openValves($i, $mySteps, $distancesWorthVisiting, $valves, $valvesForMe);
        } while ($i < count($mySteps));

        $myBestReleasedPressure = (new Collection($mySteps))
            ->map(fn (array $step) => (new Collection($step[2]))->sum())
            ->sortDesc()
            ->first();

        $elephantSteps = [['AA', 26, []]];
        $i = 0;

        do {
            [$i, $elephantSteps] = $this->openValves($i, $elephantSteps, $distancesWorthVisiting, $valves, $valvesForElephant);
        } while ($i < count($elephantSteps));

        $elephantBestReleasedPressure = (new Collection($elephantSteps))
            ->map(fn (array $step) => (new Collection($step[2]))->sum())
            ->sortDesc()
            ->first();

        return $myBestReleasedPressure + $elephantBestReleasedPressure;
    }
}
