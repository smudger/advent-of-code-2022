<?php

namespace Smudger\AdventOfCode2022\Day12;

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
    public function findPathFromEndToStart(): array
    {
        $steps = [new Step($this->end, 0)];
        $i = 0;

        do {
            [$i, $steps] = $this->move($i, $steps);
            $containsStart = count(array_filter($steps, fn (Step $step) => $step->position->equals($this->start))) > 0;
        } while (! $containsStart);

        return $steps;
    }

    /** @return Step[] */
    public function findPathFromEndToLowestElevation(): array
    {
        $steps = [new Step($this->end, 0)];
        $i = 0;

        do {
            [$i, $steps] = $this->move($i, $steps);
            $containsLowestElevation = count(array_filter($steps, fn (Step $step) => $this->heightAt($step->position) === ord('a'))) > 0;
        } while (! $containsLowestElevation);

        return $steps;
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
}
