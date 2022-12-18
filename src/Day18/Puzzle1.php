<?php

namespace Smudger\AdventOfCode2022\Day18;

use Exception;
use Illuminate\Support\Collection;

class Puzzle1
{
    public function __invoke(string $fileName)
    {
        $input = file_get_contents(__DIR__.'/'.$fileName)
            ?: throw new Exception('Failed to read input file.');
        $points = array_map(fn (string $line) => array_map('intval', explode(',', $line)), explode("\n", $input));
        $shape = [];
        foreach ($points as $point) {
            $shape[$point[0]][$point[1]][$point[2]] = 1;
        }

        return (new Collection($points))
            ->map(function (array $point) use ($shape) {
                $openFaces = 6;
                [$x, $y, $z] = $point;

                if (isset($shape[$x + 1][$y][$z])) {
                    $openFaces--;
                }

                if (isset($shape[$x - 1][$y][$z])) {
                    $openFaces--;
                }

                if (isset($shape[$x][$y + 1][$z])) {
                    $openFaces--;
                }

                if (isset($shape[$x][$y - 1][$z])) {
                    $openFaces--;
                }

                if (isset($shape[$x][$y][$z + 1])) {
                    $openFaces--;
                }

                if (isset($shape[$x][$y][$z - 1])) {
                    $openFaces--;
                }

                return $openFaces;
            })
            ->sum();
    }
}
