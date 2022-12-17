<?php

namespace Smudger\AdventOfCode2022\Day17;

use Exception;

class Puzzle2
{
    public function __invoke(string $fileName)
    {
        $desiredRockCount = 1000000000000;
        $input = file_get_contents(__DIR__.'/'.$fileName)
            ?: throw new Exception('Failed to read input file.');
        $moves = new Moves($input);
        $rocks = new Rocks();

        $cave = [0 => range(0, 8)];

        $rockAndMoves = [];

        $i = 1;
        while (true) {
            $rock = $rocks->get($i);
            $rockIndex = match ($rock::class) {
                Horizontal::class => 0,
                Plus::class => 1,
                Letter::class => 2,
                Vertical::class => 3,
                Square::class => 4,
            };

            krsort($cave);

            $rock->spawnAt(array_keys($cave)[0] + 4);

            [$move, $moveIndex] = $moves->next();

            if (! isset($rockAndMoves[$rockIndex][$moveIndex])) {
                $rockAndMoves[$rockIndex][$moveIndex] = 1;

                do {
                    $move ??= $moves->next()[0];
                    $didMove = $rock
                        ->move($move, $cave)
                        ->drop($cave);
                    $move = null;
                } while ($didMove);

                foreach ($rock->positions() as $position) {
                    $cave[$position[0]][$position[1]] = '#';
                }

                $i++;

                continue;
            }

            $moves->backOne();
            break;
        }

        $lastSuccessfulRock = $i - 1;
        krsort($cave);

        $rocksInSetupCycle = $lastSuccessfulRock;
        $heightInSetupCycle = array_keys($cave)[0];

        $caveToCycleIn = $cave;

        $rockAndMoves = [];

        $j = $rocksInSetupCycle + 1;
        while (true) {
            $rock = $rocks->get($j);
            $rockIndex = match ($rock::class) {
                Horizontal::class => 0,
                Plus::class => 1,
                Letter::class => 2,
                Vertical::class => 3,
                Square::class => 4,
            };

            krsort($caveToCycleIn);

            $rock->spawnAt(array_keys($caveToCycleIn)[0] + 4);

            [$move, $moveIndex] = $moves->next();

            if (! isset($rockAndMoves[$rockIndex][$moveIndex])) {
                $rockAndMoves[$rockIndex][$moveIndex] = 1;

                do {
                    $move ??= $moves->next()[0];
                    $didMove = $rock
                        ->move($move, $caveToCycleIn)
                        ->drop($caveToCycleIn);
                    $move = null;
                } while ($didMove);

                foreach ($rock->positions() as $position) {
                    $caveToCycleIn[$position[0]][$position[1]] = '#';
                }

                $j++;

                continue;
            }

            $moves->backOne();
            break;
        }

        $lastSuccessfulRockInCycle = $j - 1;
        krsort($caveToCycleIn);

        $heightInNormalCycle = array_keys($caveToCycleIn)[0] - $heightInSetupCycle;
        $rocksInNormalCycle = $lastSuccessfulRockInCycle - $lastSuccessfulRock;

        $rockCountAfterSetupCycle = $desiredRockCount - $rocksInSetupCycle;
        $numberOfCyclesToAdd = floor($rockCountAfterSetupCycle / $rocksInNormalCycle);
        $numberOfRocksToFinishWith = $rockCountAfterSetupCycle % $rocksInNormalCycle;

        $finishingCave = $cave;

        for ($k = $rocksInSetupCycle + 1; $k <= $rocksInSetupCycle + $numberOfRocksToFinishWith; $k++) {
            $rock = $rocks->get($k);

            krsort($finishingCave);

            $rock->spawnAt(array_keys($finishingCave)[0] + 4);

            do {
                $didMove = $rock
                    ->move($moves->next()[0], $finishingCave)
                    ->drop($finishingCave);
            } while ($didMove);

            foreach ($rock->positions() as $position) {
                $finishingCave[$position[0]][$position[1]] = '#';
            }
        }

        krsort($finishingCave);
        $finishingCaveHeight = array_keys($finishingCave)[0] - $heightInSetupCycle;

        $totalHeight = $heightInSetupCycle + $finishingCaveHeight + ($numberOfCyclesToAdd * $heightInNormalCycle);

        return $totalHeight;
    }
}
