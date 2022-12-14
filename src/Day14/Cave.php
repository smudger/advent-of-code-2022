<?php

namespace Smudger\AdventOfCode2022\Day14;

use Illuminate\Support\Collection;

class Cave
{
    private Collection $rocks;

    private Collection $sand;

    private int $bedrock;

    public function __construct(string $input)
    {
        $this->rocks = (new Collection(explode("\n", $input)))
            ->map(fn (string $line) => explode(' -> ', $line))
            ->map(fn (array $points) => array_map(null, array_slice($points, 0, count($points) - 1), array_slice($points, 1)))
            ->flatMap(fn (array $lengths) => (new Collection($lengths))->flatMap(fn (array $pair) => $this->line($pair)))
            ->unique(fn (Position $position) => "$position->x,$position->y")
            ->values();

        $this->bedrock = $this->rocks
            ->sort(fn (Position $a, Position $b) => ($a->y > $b->y) - ($a->y < $b->y))
            ->last()
            ->y;

        $this->sand = new Collection([]);
    }

    /** @return Position[] */
    private function line(array $pair): array
    {
        $start = new Position(...array_map('intval', explode(',', $pair[0])));
        $end = new Position(...array_map('intval', explode(',', $pair[1])));

        return $start->lineTo($end);
    }

    public function moveSand(Position $start): Position|false
    {
        if (! $this->isFilled($start->down())) {
            if ($start->down()->y === $this->bedrock) {
                return false;
            }

            return $this->moveSand($start->down());
        }

        if (! $this->isFilled($start->downLeft())) {
            if ($start->downLeft()->y === $this->bedrock) {
                return false;
            }

            return $this->moveSand($start->downLeft());
        }

        if (! $this->isFilled($start->downRight())) {
            if ($start->downRight()->y === $this->bedrock) {
                return false;
            }

            return $this->moveSand($start->downRight());
        }

        return $start;
    }

    private function isFilled(Position $position): bool
    {
        return $this->rocks
            ->filter(fn (Position $rock) => $rock->equals($position))
            ->isNotEmpty()
            || $this->sand
            ->filter(fn (Position $sand) => $sand->equals($position))
            ->isNotEmpty();
    }

    public function addSand(Position $sand): void
    {
        $this->sand->push($sand);
    }

    public function sandCount(): int
    {
        return $this->sand->count();
    }
}
