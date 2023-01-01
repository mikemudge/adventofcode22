<?php

include_once __DIR__ . "/../helpers/Grid.php";

$lines = file(dirname(__FILE__) . "/input");
//$lines = file(dirname(__FILE__) . "/sample");
$part1 = 0;
$part2 = 0;

$lines = array_map("rtrim", $lines);

class Elf {
    public $x;
    public $y;
    private $move = null;

    public function __construct($x, $y) {
        $this->x = $x;
        $this->y = $y;
    }

    public function proposeMove($grid, $order) {
        $this->move = null;
        $neighbours = [
            [-1, -1], [0, -1], [1, -1],
            [-1, 0], [1, 0],
            [-1, 1], [0, 1], [1, 1],
        ];
        $elfNeighbour = false;
        foreach ($neighbours as $loc) {
            if ($grid->get($this->x + $loc[0], $this->y + $loc[1]) == "#") {
                $elfNeighbour = true;
            }
        }
        if (!$elfNeighbour) {
            // If no surrounding elves, then don't move.
            return null;
        }

        foreach ($order as $dir) {
            $locs = match ($dir) {
                'N' => [[-1, -1], [0, -1], [1, -1]],
                'S' => [[-1, 1], [0, 1], [1, 1]],
                'W' => [[-1, -1], [-1, 0], [-1, 1]],
                'E' => [[1, -1], [1, 0], [1, 1]],
                default => throw new RuntimeException("Unknown dir $dir"),

            };

            $hasElf = false;
            foreach($locs as $loc) {
                if ($grid->get($this->x + $loc[0], $this->y + $loc[1]) == "#") {
                    $hasElf = true;
                };
            }
            if (!$hasElf) {
                $x = $this->x + $locs[1][0];
                $y = $this->y + $locs[1][1];
                $this->move = [$x, $y];
                return $this->move;
            }
        }
        // No move possible.
    }

    public function cancelMove() {
        $this->move = null;
    }

    public function move(Grid $grid): bool {
        if ($this->move) {
            $grid->set($this->x, $this->y, ".");
            $this->x = $this->move[0];
            $this->y = $this->move[1];
            $grid->set($this->x, $this->y, "#");
            return true;
        }
        return false;
    }
}

// Need my grid to be larger/growable.
$grid = new Grid(1000, 1000, ".");
$elves = [];
foreach($lines as $y=>$line) {
    $row = str_split($line);
    foreach ($row as $x => $v) {
        if ($v == "#") {
            $elves[] = new Elf(500 + $x, 500 + $y);
        }
        $grid->set(500 + $x, 500 + $y, $v);
    }
}

echo("Initial State\n");
$grid->print(490, 495, 30, 17);

$moved = true;
$order = ['N', 'S', 'W', 'E'];
for ($r = 1;; $r++) {
    $moves = new Grid($grid->getWidth(), $grid->getHeight(), null);
    $moved = false;
    foreach($elves as $elf) {
        $move = $elf->proposeMove($grid, $order);
        if (!$move) {
            continue;
        }
        // At least 1 move was proposed, need to continue iterating
        $moved = true;
        // If move location already proposed, then cancel it for both.
        $moveTo = $moves->get($move[0], $move[1]);
        if (!$moveTo) {
            $moves->set($move[0], $move[1], $elf);
        } else {
            $moveTo->cancelMove();
            $elf->cancelMove();
        }
    }

    if (!$moved) {
        // No one moved or even tried too.
        break;
    }
    foreach($elves as $elf) {
        // Perform move if it hasn't cancelled.
        $elf->move($grid);
    }
    // show progress.
    if ($r <= 10) {
        echo("End of round $r\n");
        $grid->print(495, 495, 100, 20);
    }
    // Rotate the order.
    $order[] = array_shift($order);
    // calculate part 1 after 10 rounds.
    if ($r == 10) {
        $x1 = $grid->getWidth();
        $x2 = 0;
        $y1 = $grid->getHeight();
        $y2 = 0;
        foreach ($elves as $elf) {
            $x1 = min($elf->x, $x1);
            $x2 = max($elf->x, $x2);
            $y1 = min($elf->y, $y1);
            $y2 = max($elf->y, $y2);
        }
        for ($y = $y1; $y <= $y2; $y++) {
            for ($x = $x1; $x <= $x2; $x++) {
                if ($grid->get($x, $y) == ".") {
                    $part1++;
                }
            }
        }
    }
}
$part2 = $r;
// Need to iterate elves movements one round at a time.


// 6806 is too high.
echo "Part 1: $part1\n";
echo "Part 2: $part2\n";
