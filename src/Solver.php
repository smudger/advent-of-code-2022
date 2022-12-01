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
        if (! class_exists($class)) {
            self::error("Could not find class to solve Day $day Puzzle $puzzle.");
            self::info("Tried to instantiate: $class");
            exit(1);
        }

        $instance = new $class();
        if (! is_callable($instance)) {
            self::error("Instantiated class is not callable: $class");
            exit(1);
        }

        $startTime = microtime(true);
        self::info($instance('input.txt'));
        $endTime = microtime(true);

        $elapsed = round($endTime - $startTime, 4);
        self::info("Completed in $elapsed seconds.");
    }

    private static function error(string $message): void
    {
        echo "\033[01;31m  $message  \033[0m\n";
    }

    private static function info(string $message): void
    {
        echo "  $message  \n";
    }
}
