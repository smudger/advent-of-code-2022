<?php

namespace Smudger\AdventOfCode2022\Day12;

use Illuminate\Support\Collection;

class Step
{
    public function __construct(
        public readonly Position $position,
        public readonly int $count,
    ) {
    }

    /** @return Step[] */
    public function nextSteps(Grid $grid): array
    {
        $adjacentSteps = new Collection([]);
        if ($this->position->x > $grid->leftBound) {
            $adjacentSteps->push(new Step(new Position($this->position->x - 1, $this->position->y), $this->count + 1));
        }
        if ($this->position->x < $grid->rightBound) {
            $adjacentSteps->push(new Step(new Position($this->position->x + 1, $this->position->y), $this->count + 1));
        }
        if ($this->position->y > $grid->topBound) {
            $adjacentSteps->push(new Step(new Position($this->position->x, $this->position->y - 1), $this->count + 1));
        }
        if ($this->position->y < $grid->bottomBound) {
            $adjacentSteps->push(new Step(new Position($this->position->x, $this->position->y + 1), $this->count + 1));
        }

        return $adjacentSteps
            ->filter(fn (Step $step) => $grid->heightAt($step->position) >= $grid->heightAt($this->position) - 1)
            ->values()
            ->all();
    }
}
