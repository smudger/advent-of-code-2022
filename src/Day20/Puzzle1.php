<?php

namespace Smudger\AdventOfCode2022\Day20;

use Exception;

class Puzzle1
{
    public function __invoke(string $fileName)
    {
        $input = file_get_contents(__DIR__.'/'.$fileName)
            ?: throw new Exception('Failed to read input file.');
        $original = array_map('intval', explode("\n", $input));
        $size = count($original);

        $decrypted = array_map(fn (int $value, int $key) => [$key, $value], $original, array_keys($original));

        foreach ($original as $key => $value) {
            $decrypted = $this->jump($decrypted, $size, $key, $value);
        }

        $decryptedValues = array_map(fn (array $entry) => $entry[1], $decrypted);
        $indexOfZero = array_search(0, $decryptedValues);

        return array_sum(array_map(fn (int $index) => $decryptedValues[($indexOfZero + $index) % count($decryptedValues)], [1000, 2000, 3000]));
    }

    private function jump(array $decrypted, int $size, int $key, int $value): array
    {
        $currentIndex = array_search([$key, $value], $decrypted);
        $newIndex = ($value + $currentIndex) % ($size - 1);
        $decrypted = array_values(array_filter($decrypted, fn (array $entry) => $entry[0] !== $key));
        array_splice($decrypted, $newIndex, 0, [[$key, $value]]);

        return array_values($decrypted);
    }
}
