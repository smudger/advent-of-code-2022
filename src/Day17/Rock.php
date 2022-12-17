<?php

namespace Smudger\AdventOfCode2022\Day17;

interface Rock
{
    public function spawnAt(int $baseHeight): void;

    public function move(Move $move, array $cave): self;

    public function drop(array $cave): bool;

    public function positions(): array;
}
