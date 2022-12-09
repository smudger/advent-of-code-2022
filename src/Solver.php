<?php

namespace Smudger\AdventOfCode2022;

use Illuminate\Support\Collection;

class Solver
{
    public static function solve(\Composer\Script\Event $event): void
    {
        $args = $event->getArguments();

        if ($args[0] === 'all') {
            self::runAll();
        } else {
            $day = $args[0];
            $puzzle = $args[1];

            self::run($day, $puzzle);
        }
    }

    private static function runAll(): void
    {
        $startTime = microtime(true);
        (new Collection(scandir(__DIR__)))
            ->filter(fn (string $dir) => str_starts_with($dir, 'Day'))
            ->map(fn (string $dir) => substr($dir, 3))
            ->each(function (string $day) {
                self::run($day, '1');
                self::info('');
                self::run($day, '2');
                self::info('');
            });
        $endTime = microtime(true);

        $elapsed = round($endTime - $startTime, 4);
        self::success("Completed all solutions in $elapsed seconds.");
    }

    private static function run($day, $puzzle): void
    {
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

        $prettyDay = str_pad($day, 2, '0', STR_PAD_LEFT);
        self::info("========= Day $prettyDay, Puzzle $puzzle =========");

        $startTime = microtime(true);
        self::success($instance('input.txt'));
        $endTime = microtime(true);

        $elapsed = round($endTime - $startTime, 4);
        self::info("Completed in $elapsed seconds.");
        self::info('====================================');
    }

    private static function success(string $message): void
    {
        echo "\033[01;32m  $message  \033[0m\n";
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
