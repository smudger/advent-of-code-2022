<?php

namespace Smudger\AdventOfCode2022\Day10;

use Exception;
use Illuminate\Support\Collection;

class Puzzle2
{
    public function __invoke(string $fileName)
    {
        $input = file_get_contents(__DIR__.'/'.$fileName)
            ?: throw new Exception('Failed to read input file.');
        $commands = explode("\n", $input);

        (new Collection($commands))
            ->reduce(function (Collection $carry, string $command) {
                if ($command === 'noop') {
                    return $carry->push($carry->last());
                }

                $change = intval(explode(' ', $command)[1]);
                $carry->push($carry->last());

                return $carry->push($carry->last() + $change);
            }, new Collection([0, 1]))
            ->each(function (int $sprintCentre, int $cycle) {
                if ($cycle === 0 || $cycle > 240) {
                    return;
                }

                $renderPosition = ($cycle % 40) - 1;
                $spriteIsHere = in_array($renderPosition, [$sprintCentre - 1, $sprintCentre, $sprintCentre + 1]);
                $stringToRender = $spriteIsHere ? '#' : '.';

                if ($cycle % 40 === 0) {
                    $stringToRender .= "\n";
                }
                if ($cycle % 40 === 1) {
                    $stringToRender = '  '.$stringToRender;
                }

                echo $stringToRender;
            });

        return 'Done!';
    }
}
