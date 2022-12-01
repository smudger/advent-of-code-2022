<?php

namespace Smudger\AdventOfCode2022;

class Solver
{
    public static function solve(\Composer\Script\Event $event): void
    {
        $args = $event->getArguments();
        $day = $args[0];
        $puzzle = $args[1];

        $class = "Smudger\\AdventOfCode2022\\Day$day\\Puzzle$puzzle";
        $instance = new $class();

        $startTime = microtime(true);
        echo is_callable($instance) ? $instance('input.txt') : 'Instantiated class is not callable.';
        $endTime = microtime(true);

        $elapsed = round($endTime-$startTime, 4);
        echo "\nCompleted in $elapsed seconds.\n";
    }
}
