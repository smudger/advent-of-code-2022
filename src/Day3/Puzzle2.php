<?php

namespace Smudger\AdventOfCode2022\Day3;

use Exception;
use Illuminate\Support\Collection;

class Puzzle2
{
    public function __invoke(string $fileName): int
    {
        $input = file_get_contents(__DIR__.'/'.$fileName)
            ?: throw new Exception('Failed to read input file.');

        return (new Collection(explode("\n", $input)))
            ->filter()
            ->chunk(3)
            ->map(function (Collection $elves) {
                return (new Collection(str_split($elves->first())))
                    ->first(fn (string $char) => str_contains($elves->skip(1)->first(), $char)
                        && str_contains($elves->skip(2)->first(), $char));
            })
            ->map(fn (string $char) => $char === strtolower($char)
                ? (1 + ord($char)) - ord('a')
                : (27 + ord($char)) - ord('A')
            )
            ->sum();
    }
}
