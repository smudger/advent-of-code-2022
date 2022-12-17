<?php

namespace Smudger\AdventOfCode2022\Day17;

use Exception;

class CaveHeight
{
    public function __invoke(string $fileName, int $desiredRockCount)
    {
        $input = file_get_contents(__DIR__.'/'.$fileName)
            ?: throw new Exception('Failed to read input file.');
        $moves = new Moves($input);

        [$rocksInBaseCycle, $heightInBaseCycle, $baseCave] = $this->generateBaseCave($moves);

        $caveToCycleIn = $baseCave;
        [$rocksAfterLoopingCycle, $heightAfterLoopingCycle] = $this->calculateLoopingCycle($caveToCycleIn, $rocksInBaseCycle + 1, $moves);
        $rocksInLoopingCycle = $rocksAfterLoopingCycle - $rocksInBaseCycle;
        $heightInLoopingCycle = $heightAfterLoopingCycle - $heightInBaseCycle;

        $rocksRemainingAfterBaseCycle = $desiredRockCount - $rocksInBaseCycle;
        $numberOfCyclesToAdd = floor($rocksRemainingAfterBaseCycle / $rocksInLoopingCycle);
        $rocksInFinalCycle = $rocksRemainingAfterBaseCycle % $rocksInLoopingCycle;

        $outOfLoopHeight = $this->finishBaseCave(
            $baseCave,
            $rocksInBaseCycle + 1,
            $rocksInBaseCycle + $rocksInFinalCycle,
            $moves,
        );

        return $outOfLoopHeight + ($numberOfCyclesToAdd * $heightInLoopingCycle);
    }

    private function generateBaseCave(Moves $moves)
    {
        $cave = [0 => range(0, 8)];
        $rocks = new Rocks();
        $seenRockAndMoveCombinations = [];

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

            if (! isset($seenRockAndMoveCombinations[$rockIndex][$moveIndex])) {
                $seenRockAndMoveCombinations[$rockIndex][$moveIndex] = 1;

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

        return [$lastSuccessfulRock, array_keys($cave)[0], $cave];
    }

    private function calculateLoopingCycle(array $caveToCycleIn, int $rockToStartAt, Moves $moves)
    {
        $seenRockAndMoveCombinations = [];
        $rocks = new Rocks();

        $j = $rockToStartAt;
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

            if (! isset($seenRockAndMoveCombinations[$rockIndex][$moveIndex])) {
                $seenRockAndMoveCombinations[$rockIndex][$moveIndex] = 1;

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

        return [$lastSuccessfulRockInCycle, array_keys($caveToCycleIn)[0]];
    }

    private function finishBaseCave(array $baseCave, int $start, int $end, Moves $moves)
    {
        $rocks = new Rocks();

        for ($k = $start; $k <= $end; $k++) {
            $rock = $rocks->get($k);

            krsort($baseCave);

            $rock->spawnAt(array_keys($baseCave)[0] + 4);

            do {
                $didMove = $rock
                    ->move($moves->next()[0], $baseCave)
                    ->drop($baseCave);
            } while ($didMove);

            foreach ($rock->positions() as $position) {
                $baseCave[$position[0]][$position[1]] = '#';
            }
        }

        krsort($baseCave);

        return array_keys($baseCave)[0];
    }
}
