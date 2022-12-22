<?php

namespace Smudger\AdventOfCode2022\Day22;

class Grid
{
    private array $grid;

    private array $boundsPerY;

    private array $boundsPerX;

    public function __construct(string $raw)
    {
        $rows = explode("\n", $raw);
        $this->boundsPerX = [];
        $this->boundsPerY = [];

        foreach ($rows as $y => $row) {
            if (! isset($this->boundsPerY[$y + 1])) {
                $this->boundsPerY[$y + 1] = [10000, 0];
            }
            foreach (str_split($row) as $x => $char) {
                if (! isset($this->boundsPerX[$x + 1])) {
                    $this->boundsPerX[$x + 1] = [10000, 0];
                }
                if (strlen(trim($char)) > 0) {
                    $this->grid[$y + 1][$x + 1] = $char;
                    if ($y + 1 > $this->boundsPerX[$x + 1][1]) {
                        $this->boundsPerX[$x + 1][1] = $y + 1;
                    }
                    if ($y + 1 < $this->boundsPerX[$x + 1][0]) {
                        $this->boundsPerX[$x + 1][0] = $y + 1;
                    }
                    if ($x + 1 > $this->boundsPerY[$y + 1][1]) {
                        $this->boundsPerY[$y + 1][1] = $x + 1;
                    }
                    if ($x + 1 < $this->boundsPerY[$y + 1][0]) {
                        $this->boundsPerY[$y + 1][0] = $x + 1;
                    }
                }
            }
        }
    }

    public function followInstruction(array $position, string|int $instruction): array
    {
//        $prettyPos = implode(", ", $position);
//        var_dump("[$prettyPos]: $instruction");
        if (is_int($instruction)) {
            return $this->move($position, $instruction);
        } else {
            return match ($instruction) {
                'L' => [$position[0], $position[1], ($position[2] + 3) % 4],
                'R' => [$position[0], $position[1], ($position[2] + 1) % 4],
            };
        }
    }

    public function move(array $position, int $times): array
    {
        $direction = match ($position[2]) {
            0 => [0, 1],        // right
            1 => [1, 0],        // down
            2 => [0, -1],        // left
            3 => [-1, 0],        // up
        };

        for ($i = 1; $i <= $times; $i++) {
            $position = $this->hop($position, $direction);
        }

        return $position;
    }

    private function hop(array $position, array $direction): array
    {
        $newY = $position[0] + $direction[0];
        $newX = $position[1] + $direction[1];

        if ($newY < $this->boundsPerX[$position[1]][0]) {
            // wrap from top to bottom
            $wrappedNewY = $this->boundsPerX[$position[1]][1];

            return $this->hopToIfAble($wrappedNewY, $position[1], $position);
        }
        if ($newY > $this->boundsPerX[$position[1]][1]) {
            // wrap from bottom to top
            $wrappedNewY = $this->boundsPerX[$position[1]][0];

            return $this->hopToIfAble($wrappedNewY, $position[1], $position);
        }
        if ($newX < $this->boundsPerY[$position[0]][0]) {
            // wrap from left to right
            $wrappedNewX = $this->boundsPerY[$position[0]][1];

            return $this->hopToIfAble($position[0], $wrappedNewX, $position);
        }
        if ($newX > $this->boundsPerY[$position[0]][1]) {
            // wrap from right to left
            $wrappedNewX = $this->boundsPerY[$position[0]][0];

            return $this->hopToIfAble($position[0], $wrappedNewX, $position);
        }

        // simple move - checking wall!
        return $this->hopToIfAble($newY, $newX, $position);
    }

    public function hopToIfAble(int $y, int $x, array $originalPosition): array
    {
        if ($this->grid[$y][$x] === '.') {
            return [$y, $x, $originalPosition[2]];
        }

        if ($this->grid[$y][$x] === '#') {
            return $originalPosition;
        }

        throw new \Exception('TRIED TO MOVE OFF MAP!');
    }
}
