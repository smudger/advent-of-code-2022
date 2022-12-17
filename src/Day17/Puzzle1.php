<?php

namespace Smudger\AdventOfCode2022\Day17;

use Exception;

class Puzzle1
{
    public function __invoke(string $fileName)
    {
        $input = file_get_contents(__DIR__.'/'.$fileName)
            ?: throw new Exception('Failed to read input file.');
        $moves = new Moves($input);
        $rocks = new Rocks();

        $cave = [0 => range(0, 8)];

        for ($i = 1; $i <= 2022; $i++) {
            $rock = $rocks->get($i);

            krsort($cave);

            $rock->spawnAt(array_keys($cave)[0] + 4);

            do {
                $didMove = $rock
                    ->move($moves->next(), $cave)
                    ->drop($cave);
            } while ($didMove);

            foreach ($rock->positions() as $position) {
                $cave[$position[0]][$position[1]] = '#';
            }
//            krsort($cave);
//            echo "=================\n";
//            foreach ($cave as $row) {
//                foreach (range(0, 8) as $x) {
//                    if ($x === 0 || $x === 8) {
//                        echo '|';
//                        continue;
//                    }
//                    echo isset($row[$x]) ? '#' : '.';
//                }
//                echo "\n";
//            }
//            echo "=================\n";
        }

        krsort($cave);

        return array_keys($cave)[0];
    }
}
