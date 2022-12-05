<?php

namespace Smudger\AdventOfCode2022\Day5;

use Illuminate\Support\Collection;

class Warehouse
{
    private Collection $stacks;

    public function __construct(string $layout)
    {
        $arrayLayout = array_map('str_split', explode("\n", $layout));
        $transposed = array_map(null, ...$arrayLayout);

        $this->stacks = (new Collection($transposed))
            ->map(fn (array $column) => array_reverse($column))
            ->filter(fn (array $column) => trim($column[0]) !== '')
            ->map(fn (array $column) => array_filter($column, fn (string $crate) => trim($crate) !== ''))
            ->mapWithKeys(fn (array $column) => [$column[0] => new Collection(array_slice($column, 1))]);
    }

    public function moveIndividually(Move $move): void
    {
        for ($i = 0; $i < $move->times; $i++) {
            $crate = $this->stacks->get($move->from)->pop();
            $this->stacks->get($move->to)->push($crate);
        }
    }

    public function moveTogether(Move $move): void
    {
        $fromStack = $this->stacks->get($move->from);
        $crates = $fromStack->splice($fromStack->count() - $move->times);
        $this->stacks->put($move->from, $fromStack);

        $toStack = $this->stacks->get($move->to);
        $toStack = $toStack->merge($crates);
        $this->stacks->put($move->to, $toStack);
    }

    public function view(): void
    {
        var_dump('=== WAREHOUSE ===');
        $this->stacks->each(function (Collection $stack, string $id) {
            var_dump($id.': '.implode(', ', $stack->all()));
        });
        var_dump('=================');
    }

    public function topCrates(): string
    {
        return implode('', $this->stacks->map(fn (Collection $stack) => $stack->last())->all());
    }
}
