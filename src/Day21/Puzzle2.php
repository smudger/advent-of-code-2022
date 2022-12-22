<?php

namespace Smudger\AdventOfCode2022\Day21;

use Exception;
use Illuminate\Support\Collection;

class Puzzle2
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

        unset($monkeys['humn']);

        return $this->inverseShout('humn', $monkeys);
    }

    private function shout(string $name, array $monkeys): float
    {
        $monkey = $monkeys[$name];

        if (is_int($monkey) || is_float($monkey)) {
            return $monkey;
        }

        return match ($monkey[1]) {
            '+' => $this->shout($monkey[0], $monkeys) + $this->shout($monkey[2], $monkeys),
            '-' => $this->shout($monkey[0], $monkeys) - $this->shout($monkey[2], $monkeys),
            '*' => $this->shout($monkey[0], $monkeys) * $this->shout($monkey[2], $monkeys),
            '/' => $this->shout($monkey[0], $monkeys) / $this->shout($monkey[2], $monkeys),
        };
    }

    private function inverseShout(string $newRoot, array $monkeys): int
    {
        if (in_array($newRoot, $monkeys['root'])) {
            return $monkeys['root'][0] === $newRoot
                 ? $this->shout($monkeys['root'][2], $monkeys)
                 : $this->shout($monkeys['root'][0], $monkeys);
        }

        if (isset($monkeys[$newRoot]) && (is_int($monkeys[$newRoot]) || is_float($monkeys[$newRoot]))) {
            return $monkeys[$newRoot];
        }

        $nextEquation = array_values(array_filter($monkeys, fn (int|array $equation) => is_array($equation) && ($equation[0] === $newRoot || $equation[2] === $newRoot)))[0];
        $nextEquationSolution = array_search($nextEquation, $monkeys);
        [$indexOfRoot, $indexOfOther] = $nextEquation[0] === $newRoot
            ? [0, 2]
            : [2, 0];

        return match ($nextEquation[1]) {
            '+' => $this->inverseShout($nextEquationSolution, $monkeys) - $this->shout($nextEquation[$indexOfOther], $monkeys),
            '*' => $this->inverseShout($nextEquationSolution, $monkeys) / $this->shout($nextEquation[$indexOfOther], $monkeys),
            '-' => $indexOfRoot === 0
                    ? $this->inverseShout($nextEquationSolution, $monkeys) + $this->shout($nextEquation[$indexOfOther], $monkeys)
                    : $this->shout($nextEquation[$indexOfOther], $monkeys) - $this->inverseShout($nextEquationSolution, $monkeys),
            '/' => $indexOfRoot === 0
                ? $this->inverseShout($nextEquationSolution, $monkeys) * $this->shout($nextEquation[$indexOfOther], $monkeys)
                : $this->shout($nextEquation[$indexOfOther], $monkeys) / $this->inverseShout($nextEquationSolution, $monkeys),
        };
    }
}
