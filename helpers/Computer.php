<?php

class Computer {
    private int $totalCycles;
    private array $instructions;
    private int $instructionPointer;
    private int $x;
    /**
     * @var int[]
     */
    private array $costs;

    /**
     * @param array $instructions
     */
    public function __construct(array $instructions) {
        $this->instructions = $instructions;
        $this->instructionPointer = 0;
        $this->x = 1;
        $this->totalCycles = 0;
        $this->costs = [
            'noop' => 1,
            'addx' => 2,
        ];
    }

    public function executeToCycle(int $cycles): void {
        while ($this->instructionPointer < count($this->instructions)) {
            $instruction = $this->instructions[$this->instructionPointer];
            $parts = explode(" ", $instruction);
            $cost = $this->costs[$parts[0]];
            if ($this->totalCycles + $cost >= $cycles) {
                return;
            }
            $i = $this->totalCycles;
            $x = $this->x;
            echo("$instruction - $i $x\n");
            $this->execute($parts);
            $this->totalCycles += $cost;
            $this->instructionPointer++;
        }
    }
    private function execute(array $parts): void {
        switch($parts[0]) {
            case "noop":
                break;
            case "addx":
                $this->x += intval($parts[1]);
                break;
            default:
                throw new Exception("Unknown instruction " . $parts[0]);
        }
    }

    public function getX() {
        return $this->x;
    }

    public function isRunning() {
        return $this->instructionPointer < count($this->instructions);
    }
}