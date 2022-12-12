<?php

namespace Smudger\AdventOfCode2022\Day12;

use Closure;
use Illuminate\Support\Collection;

class Grid
{
    private readonly array $grid;

    public readonly Position $start;

    private readonly Position $end;

    public readonly int $leftBound;

    public readonly int $rightBound;

    public readonly int $topBound;

    public readonly int $bottomBound;

    public function __construct(string $input)
    {
        $this->grid = array_map('str_split', explode("\n", $input));
        $this->leftBound = 0;
        $this->rightBound = count($this->grid[0]) - 1;
        $this->topBound = 0;
        $this->bottomBound = count($this->grid) - 1;

        $this->findStartAndEnd();
    }

    private function findStartAndEnd(): void
    {
        foreach ($this->grid as $y => $row) {
            $x = array_search('S', $row);
            if ($x !== false) {
                $this->start = new Position($x, $y);
            }
            $x = array_search('E', $row);
            if ($x !== false) {
                $this->end = new Position($x, $y);
            }
        }
    }

    /** @return Step[] */
    public function findPathFromEnd(Closure $goal): array
    {
        $steps = [new Step($this->end, 0)];
        $i = 0;

        do {
            [$i, $steps] = $this->move($i, $steps);
        } while (count(array_filter($steps, $goal)) === 0);

        $path = [array_values(array_filter($steps, $goal))[0]];
        $i = 0;

        do {
            [$i, $path] = $this->trackBack($i, $path, $steps);
        } while (count(array_filter($path, fn (Step $step) => $step->count === 0)) === 0);

        return $path;
    }

    public function heightAt(Position $position): int
    {
        $char = $this->grid[$position->y][$position->x];

        if ($char === 'S') {
            return ord('a');
        }
        if ($char === 'E') {
            return ord('z');
        }

        return ord($char);
    }

    private function move(int $i, array $steps): array
    {
        $nextSteps = $steps[$i]->nextSteps($this);

        $stepsToAdd = (new Collection($nextSteps))
            ->filter(fn (Step $nextStep) => count(array_filter($steps, fn (Step $seenStep) => $seenStep->position->equals($nextStep->position))) === 0)
            ->values()
            ->all();

        return [$i + 1, array_merge($steps, $stepsToAdd)];
    }

    private function trackBack(int $i, array $path, array $steps)
    {
        $stepToAdd = $path[$i]->stepToAdd($steps);

        $path[] = $stepToAdd;

        return [$i + 1, $path];
    }

    /** @param Step[] */
    public function prettyPrint(array $path): void
    {
        $green = "\033[01;31m";
        $noColour = "\033[0m";

        foreach ($this->grid as $y => $row) {
            $line = '  ';
            foreach ($row as $x => $char) {
                $here = new Position($x, $y);
                $cell = $char;
                if ($here->equals($this->start)) {
                    $cell = 'S';
                }
                if ($here->equals($this->end)) {
                    $cell = 'E';
                }
                $inPath = count(array_filter($path, fn (Step $step) => $step->position->equals($here))) !== 0;
                if ($inPath) {
                    $cell = $green.$cell.$noColour;
                }
                $line .= $cell;
            }
            $line .= "\n";

            echo $line;
        }
    }
}
