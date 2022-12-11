<?php

namespace Smudger\AdventOfCode2022\Day11;

class Monkey
{
    private string $id;
    /** @var int[] */
    private array $items;
    private string $operation;
    private int $divisor;
    private string $passingMonkey;
    private string $failingMonkey;
    private int $inspectionCount = 0;
    
    public function __construct(string $definition) {
        $lines = explode("\n", $definition);
        $this->id = str_replace(['Monkey ', ':'], '', $lines[0]);
        $this->items = array_map('intval', explode(', ', str_replace('  Starting items: ', '', $lines[1])));
        $this->operation = str_replace(['  Operation: ', 'new', 'old'], ['', '$new', '$old'], $lines[2]).';';
        $this->divisor = intval(str_replace('  Test: divisible by ', '', $lines[3]));
        $this->passingMonkey = str_replace('    If true: throw to monkey ', '', $lines[4]);
        $this->failingMonkey = str_replace('    If false: throw to monkey ', '', $lines[5]);
    }

    public function __toString(): string
    {
        $itemsString = '['.implode(", ", $this->items).']';
        return "Monkey {
            id: $this->id,
            items: $itemsString,
            operation: $this->operation,
            divisor: $this->divisor,
            passingMonkey: $this->passingMonkey,
            failingMonkey: $this->failingMonkey,
            inspectionCount: $this->inspectionCount
        }";
    }

    public function takeTurn(): array
    {
        $result = [];
        foreach ($this->items as $item)
        {
            $newValue = $this->inspect($item);
            
            $monkeyToPassTo = $newValue % $this->divisor === 0
                ? $this->passingMonkey
                : $this->failingMonkey;
            
            if (! array_key_exists($monkeyToPassTo, $result)) {
                $result[$monkeyToPassTo] = [];
            }
            
            $result[$monkeyToPassTo][] = $newValue;
        }
        
        $this->items = [];
        return $result;
    }

    private function inspect(int $old): int
    {
        $new = null;
        eval($this->operation);
        $new = intval(floor($new / 3));
        $this->inspectionCount++;
        
        return $new;
    }

    /** @param int[] $items */
    public function receive(array $items): void
    {
        $this->items = array_merge($this->items, $items);
    }
    
    public function id(): string
    {
        return $this->id;
    }

    public function inspectionCount(): int
    {
        return $this->inspectionCount;
    }
}