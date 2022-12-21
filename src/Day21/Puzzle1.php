<?php

namespace Smudger\AdventOfCode2022\Day21;

use Exception;
use Illuminate\Support\Collection;

class Puzzle1
{
    public function __invoke(string $fileName)
    {
        $input = file_get_contents(__DIR__.'/'.$fileName)
            ?: throw new Exception('Failed to read input file.');
        $monkeys = (new Collection(explode("\n", $input)))
            ->mapWithKeys(function (string $monkey) {
                [$name, $calculation] = explode(':', $monkey);
                $elements = explode(' ', trim($calculation));
                if (count($elements) === 1) {
                    $elements = intval($elements[0]);
                }

                return [$name => $elements];
            })
            ->all();

        return $this->shout('root', $monkeys);
    }

    private function shout(string $name, array $monkeys): int
    {
        if (is_int($monkeys[$name])) {
            return $monkeys[$name];
        }

        return match ($monkeys[$name][1]) {
            '+' => $this->shout($monkeys[$name][0], $monkeys) + $this->shout($monkeys[$name][2], $monkeys),
            '-' => $this->shout($monkeys[$name][0], $monkeys) - $this->shout($monkeys[$name][2], $monkeys),
            '*' => $this->shout($monkeys[$name][0], $monkeys) * $this->shout($monkeys[$name][2], $monkeys),
            '/' => $this->shout($monkeys[$name][0], $monkeys) / $this->shout($monkeys[$name][2], $monkeys),
        };
    }
}
