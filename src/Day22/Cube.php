<?php

namespace Smudger\AdventOfCode2022\Day22;

class Cube
{
    private array $grid;

    private array $boundsPerY;

    private array $boundsPerX;

    public function __construct(string $raw, private readonly Glue $glue)
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
//        $prettyPos = implode(', ', $position);
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
        for ($i = 1; $i <= $times; $i++) {
            $direction = $this->getDirection($position[2]);
            $position = $this->hop($position, $direction);
        }

        return $position;
    }

    private function getDirection(int $direction): array
    {
        return match ($direction) {
            0 => [0, 1],        // right
            1 => [1, 0],        // down
            2 => [0, -1],        // left
            3 => [-1, 0],        // up
        };
    }

    private function hop(array $position, array $direction): array
    {
        $newY = $position[0] + $direction[0];
        $newX = $position[1] + $direction[1];

        $withinNet = $newY >= $this->boundsPerX[$position[1]][0]
            && $newY <= $this->boundsPerX[$position[1]][1]
            && $newX >= $this->boundsPerY[$position[0]][0]
            && $newX <= $this->boundsPerY[$position[0]][1];

        if ($withinNet) {
            // simple move - checking wall!
            return $this->hopToIfAble([$newY, $newX, $position[2]], $position);
        }

        // wrapping move
        $newPosition = $this->glue->stick([$newY, $newX, $position[2]]);

        return $this->hopToIfAble($newPosition, $position);
    }

    public function hopToIfAble(array $newPosition, array $oldPosition): array
    {
        if ($this->grid[$newPosition[0]][$newPosition[1]] === '.') {
            return $newPosition;
        }

        if ($this->grid[$newPosition[0]][$newPosition[1]] === '#') {
            return $oldPosition;
        }

        throw new \Exception('TRIED TO MOVE OFF MAP!');
    }
}
