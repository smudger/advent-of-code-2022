<?php

namespace Smudger\AdventOfCode2022\Day16;

use Exception;
use Illuminate\Support\Collection;

class Puzzle2
{
    public function __invoke(string $fileName)
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

        $steps = [[['AA', 26], ['AA', 26], []]];
        $i = 0;

        do {
            [$i, $steps] = $this->openValves($i, $steps, $distancesWorthVisiting, $valves);
        } while ($i < count($steps));

        return (new Collection($steps))
            ->map(fn (array $step) => (new Collection($step[2]))->sum())
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

    private function openValves(int $i, array $steps, array $distancesWorthVisiting, Collection $valves)
    {
        $step = $steps[$i];
        $me = $step[0];
        $elephant= $step[1];

        $nextValves = (new Collection($distancesWorthVisiting[$me[0]]))
            ->filter(fn (int $distance, string $valve) => $me[1] - ($distance + 1) > 0)
            ->filter(fn (int $distance, string $valve) => ! isset($step[2][$valve]))
            ->map(function (int $distance, string $valve) use ($me, $step, $valves) {
                $timeRemaining = $me[1] - ($distance + 1);
                $openedValves = $step[2];
                $openedValves[$valve] = $timeRemaining * $valves->get($valve)[1];

                return [[$valve, $timeRemaining], [], $openedValves];
            })
            ->flatMap(function (array $step) use ($elephant, $distancesWorthVisiting, $valves) {
                return (new Collection($distancesWorthVisiting[$elephant[0]]))
                    ->filter(fn (int $distance, string $valve) => $elephant[1] - ($distance + 1) > 0)
                    ->filter(fn (int $distance, string $valve) => ! isset($step[2][$valve]))
                    ->map(function (int $distance, string $valve) use ($elephant, $step, $valves) {
                        $timeRemaining = $elephant[1] - ($distance + 1);
                        $openedValves = $step[2];
                        $openedValves[$valve] = $timeRemaining * $valves->get($valve)[1];

                        return [$step[0], [$valve, $timeRemaining], $openedValves];
                    })
                    ->values()
                    ->all();
            })
            ->values()
            ->all();

        return [$i + 1, array_merge($steps, $nextValves)];
    }
}
