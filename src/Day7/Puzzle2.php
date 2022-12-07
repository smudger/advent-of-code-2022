<?php

namespace Smudger\AdventOfCode2022\Day7;

use Exception;
use Illuminate\Support\Collection;

class Puzzle2
{
    public function __invoke(string $fileName)
    {
        $input = file_get_contents(__DIR__.'/'.$fileName)
            ?: throw new Exception('Failed to read input file.');
        $directories = [];
        $visitedFiles = [];

        foreach (explode("\n", $input) as $line) {
            if (str_contains($line, '$ ls')) {
                continue;
            }

            if (str_contains($line, '$ cd ..')) {
                array_pop($path);

                continue;
            }

            if (str_contains($line, '$ cd')) {
                $path[] = explode(' ', $line)[2];

                continue;
            }

            [$marker, $name] = explode(' ', $line);

            if (str_contains($marker, 'dir')) {
                continue;
            }

            $fullPath = implode('<>', $path).'<>'.$name;
            if (in_array($fullPath, $visitedFiles)) {
                continue;
            }

            $partial = '';
            foreach ($path as $dir) {
                $partial .= ($dir.'<>');
                if (! array_key_exists($partial, $directories)) {
                    $directories[$partial] = 0;
                }
                $directories[$partial] += intval($marker);
            }

            $visitedFiles[] = $fullPath;
        }

        $totalSpace = 70000000;
        $usedSpace = $directories['/<>'];
        $unusedSpace = $totalSpace - $usedSpace;
        $neededSpace = 30000000;
        $extraSpaceRequired = $neededSpace - $unusedSpace;

        return (new Collection($directories))
            ->sort()
            ->first(fn (int $size) => $size >= $extraSpaceRequired);
    }
}
