<?php

namespace Smudger\AdventOfCode2022\Day25;

use Exception;
use Illuminate\Support\Collection;

class Puzzle1
{
    public function __invoke(string $fileName)
    {
        $input = file_get_contents(__DIR__.'/'.$fileName)
            ?: throw new Exception('Failed to read input file.');
        $paddedResult = (new Collection(explode("\n", $input)))
            ->reduce(fn (string $carry, string $snafu) => $this->add($carry, $snafu), '0');

        return ltrim($paddedResult, '0');
    }

    private function add(string $left, string $right): string
    {
        $left = array_reverse(str_split($left));
        $right = array_reverse(str_split($right));

        $result = array_fill(0, max(count($left), count($right)) + 1, []);

        for ($i = 0; $i < count($result); $i++) {
            $leftChar = $left[$i] ?? '0';
            $rightChar = $right[$i] ?? '0';
            $resultChar = $result[$i][0] ?? '0';

            $decimalSum = $this->toDecimal($leftChar) + $this->toDecimal($rightChar) + $this->toDecimal($resultChar);
            if ($decimalSum > 2) {
                $decimalSum -= 5;
                $result[$i + 1][] = '1';
            }
            if ($decimalSum < -2) {
                $decimalSum += 5;
                $result[$i + 1][] = '-';
            }
            $result[$i] = $this->toSnafu($decimalSum);
        }

        return implode('', array_reverse($result));
    }

    private function toDecimal(string $char): int
    {
        return match ($char) {
            '2' => 2,
            '1' => 1,
            '0' => 0,
            '-' => -1,
            '=' => -2,
        };
    }

    private function toSnafu(int $decimal): string
    {
        return match ($decimal) {
            2 => '2',
            1 => '1',
            0 => '0',
            -1 => '-',
            -2 => '=',
        };
    }
}
