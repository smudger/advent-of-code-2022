<?php

namespace Smudger\AdventOfCode2022\Day14;

use Illuminate\Support\Collection;

class EfficientCave
{
    private array $cave;

    private int $bedrock;

    public function __construct(string $input)
    {
        $rocks = (new Collection(explode("\n", $input)))
            ->map(fn (string $line) => explode(' -> ', $line))
            ->map(fn (array $points) => array_map(null, array_slice($points, 0, count($points) - 1), array_slice($points, 1)))
            ->flatMap(fn (array $lengths) => (new Collection($lengths))->flatMap(fn (array $pair) => $this->line($pair)))
            ->unique(fn (Position $position) => "$position->x,$position->y")
            ->values();

        $this->bedrock = $rocks
                ->sort(fn (Position $a, Position $b) => ($a->y > $b->y) - ($a->y < $b->y))
                ->last()
                ->y + 2;

        $this->cave = $rocks
            ->reduce(function (array $carry, Position $rock) {
                $carry[$rock->x][$rock->y] = 'R';

                return $carry;
            }, []);
    }

    /** @return Position[] */
    private function line(array $pair): array
    {
        $start = new Position(...array_map('intval', explode(',', $pair[0])));
        $end = new Position(...array_map('intval', explode(',', $pair[1])));

        return $start->lineTo($end);
    }

    public function moveSand(array $start): array|false
    {
        $down = [$start[0], $start[1] + 1];
        if (! isset($this->cave[$down[0]][$down[1]])) {
            if ($start[1] === $this->bedrock) {
                return false;
            }

            return $this->moveSand($down);
        }

        $downLeft = [$start[0] - 1, $start[1] + 1];
        if (! isset($this->cave[$downLeft[0]][$downLeft[1]])) {
            if ($start[1] === $this->bedrock) {
                return false;
            }

            return $this->moveSand($downLeft);
        }

        $downRight = [$start[0] + 1, $start[1] + 1];
        if (! isset($this->cave[$downRight[0]][$downRight[1]])) {
            if ($start[1] === $this->bedrock) {
                return false;
            }

            return $this->moveSand($downRight);
        }

        return $start;
    }

    public function moveSandWithFloor(array $start): array
    {
        if ($start[1] + 1 === $this->bedrock) {
            return $start;
        }

        $down = [$start[0], $start[1] + 1];
        if (! isset($this->cave[$down[0]][$down[1]])) {
            return $this->moveSandWithFloor($down);
        }

        $downLeft = [$start[0] - 1, $start[1] + 1];
        if (! isset($this->cave[$downLeft[0]][$downLeft[1]])) {
            return $this->moveSandWithFloor($downLeft);
        }

        $downRight = [$start[0] + 1, $start[1] + 1];
        if (! isset($this->cave[$downRight[0]][$downRight[1]])) {
            return $this->moveSandWithFloor($downRight);
        }

        return $start;
    }

    public function addSand(array $sand): void
    {
        $this->cave[$sand[0]][$sand[1]] = 'S';
    }

    public function sandCount(): int
    {
        return (new Collection($this->cave))
            ->map(fn (array $row) => (new Collection($row))->filter(fn ($val) => $val === 'S')->count())
            ->sum();
    }
}
