<?php

namespace Smudger\AdventOfCode2022\Day3;

use Exception;
use Illuminate\Support\Collection;

class Puzzle1
{
    public function __invoke(string $fileName): int
    {
        $input = file_get_contents(__DIR__.'/'.$fileName)
            ?: throw new Exception('Failed to read input file.');

        return (new Collection(explode("\n", $input)))
            ->filter()
            ->map(fn (string $contents) => [
                substr($contents, 0, strlen($contents) / 2),
                substr($contents, -1 * (strlen($contents) / 2)),
            ])
            ->map(function (array $compartments) {
                return (new Collection(str_split($compartments[0])))
                    ->first(fn (string $char) => str_contains($compartments[1], $char));
            })
            ->map(fn (string $char) => $char === strtolower($char)
                ? (1 + ord($char)) - ord('a')
                : (27 + ord($char)) - ord('A')
            )
            ->sum();
    }
}
