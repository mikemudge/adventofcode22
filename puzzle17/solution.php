<?php

$lines = file(dirname(__FILE__) . "/input");
//$lines = file(dirname(__FILE__) . "/sample");
$part1 = 0;
$part2 = 0;

$lines = array_map("trim", $lines);

$jetIdx = 0;
$jets = str_split($lines[0]);

$rockIdx = 0;
// All rocks are tight up against the left/top edges to aid spawning location.
$rocks = [
    [
        "####",
        "....",
        "....",
        "....",
    ],
    [
        ".#..",
        "###.",
        ".#..",
        "....",
    ],
    [
        "###.",
        "..#.",
        "..#.",
        "....",
    ],
    [
        "#...",
        "#...",
        "#...",
        "#...",
    ],
    [
        "##..",
        "##..",
        "....",
        "....",
    ],
];

class Tetris {
    private array $board;
    private int $currentHeight;
    private int $width;

    public function __construct() {
        $this->board = [];
        $this->currentHeight = 0;
        $this->width = 7;
    }

    public function getCurrentHeight(): int {
        return $this->currentHeight;
    }

    public function isValid(int $x, int $y, array $rock): bool {
        if ($x < 0) {
//            echo "hit left wall\n";
            return false;
        }
        if ($y < 0) {
            // Reached the bottom of the chamber.
//            echo "hit bottom\n";
            return false;
        }
        // These are known about rock sizes.
        $rw = 4;
        $rh = 4;
        for ($ry = 0; $ry < $rh; $ry++) {
            for ($rx = 0; $rx < $rw; $rx++) {
                if ($rock[$ry][$rx] == "#") {
                    // Is part of the rock.
                    if ($x + $rx >= $this->width) {
                        // Exceeds the width of the chamber
//                        echo "hit right wall\n";
                        return false;
                    }
                    if ($y + $ry < count($this->board)) {
                        // Is within the tower of rocks.
                        if ($this->board[$y + $ry][$x + $rx] == "#") {
                            // Overlaps an existing rock
//                            echo "hit another rock\n";
                            return false;
                        }
                    }
                }
            }
        }
        return true;
    }

    public function addRock(int $x, int $y, array $rock) {
        // These are known about rock sizes.
        $rw = 4;
        $rh = 4;
        for ($ry = 0; $ry < $rh; $ry++) {
            if ($y + $ry >= count($this->board)) {
//                echo("adding row at " . ($y + $ry) . "\n");
                $this->board[$y + $ry] = str_split(".......");
            }
            for ($rx = 0; $rx < $rw; $rx++) {
                if ($rock[$ry][$rx] == "#") {
                    $this->board[$y + $ry][$x + $rx] = "#";
                    // If this is higher keep track of it.
                    $this->currentHeight = max($this->currentHeight, $y + $ry + 1);
                }
            }
        }
    }

    public function printRows(int $bot, int $y) {
        for (; $y >= $bot; $y--) {
            echo $this->getRow($y) . "\n";
        }
    }

    public function getTopRows($num): string {
        $rows = "";
        for ($y = 0; $y < $num; $y++) {
            $rows .= $this->getRow($this->currentHeight - 1 - $y);
        }
        return $rows;
    }

    public function getRow(int $y) {
        if ($y < 0 || $y >= count($this->board)) {
            return '.......';
        } else {
            return join("", $this->board[$y]);
        }
    }
}
// Build this up as rocks land.
$stoppedRocks = new Tetris();

$offset = -1;
$loopSize = -1;
$heights = [];
$visited = [];
for ($r = 0; $r < 1000000000000; $r++) {
    $heights[$r] = $stoppedRocks->getCurrentHeight();
    // Spawn rock 2 off the left edge and 3 above the highest point.
    $x = 2;
    $y = $stoppedRocks->getCurrentHeight() + 3;
    $rock = $rocks[$rockIdx];
    $rockIdx = ($rockIdx + 1) % count($rocks);

    // Now fall until landed.
    $landed = false;
    while(!$landed) {
        // Follow jet
        $jet = $jets[$jetIdx];
        $jetIdx = ($jetIdx + 1) % count($jets);

        if ($jet == ">") {
//            echo "Jet of gas pushes rock right\n";
            $x++;
            if (!$stoppedRocks->isValid($x, $y, $rock)) {
                $x--;
            }
        } else if ($jet == "<") {
//            echo "Jet of gas pushes rock left\n";
            $x--;
            if (!$stoppedRocks->isValid($x, $y, $rock)) {
                $x++;
            }
        }

        // Move down.
//        echo "Rock falls 1 unit\n";
        $y--;
        if (!$stoppedRocks->isValid($x, $y, $rock)) {
            $y++;
//            echo "and comes to rest at $x,$y\n";
            $stoppedRocks->addRock($x, $y, $rock);
            $landed = true;
        }
    }
    if ($r < 3) {
        echo "After rock $r at height " . $heights[$r] . "\n";
        $stoppedRocks->printRows($stoppedRocks->getCurrentHeight() - 20, $stoppedRocks->getCurrentHeight());
    }
    $uniqueKey = "$rockIdx.$jetIdx";
    // 6 was found by trial and error (less than this finds false loops)
    $uniqueKey .= $stoppedRocks->getTopRows(6);
    if (isset($visited[$uniqueKey])) {
        if ($offset != -1) {
            if ($r > 2022 && $r > $offset + $loopSize * 2) {
                break;
            }
        } else {
            $lastTime = $visited[$uniqueKey];
            echo("Repeat at rock=$r lastTime=$lastTime\n");
            $loopSize = $r - $visited[$uniqueKey];
            $offset = $visited[$uniqueKey];
            echo("Loop from $offset of size $loopSize\n");
        }
    }
    $visited[$uniqueKey] = $r;
}

$part1 = $heights[2022];
echo "Part 1: $part1\n";

$start = $heights[$offset];
$loopHeight = $heights[$offset + $loopSize] - $start;
for ($i = 0; $i < $loopHeight; $i++) {
    $a = $start + $i;
    $b = $a + $loopHeight;
    if ($stoppedRocks->getRow($a) != $stoppedRocks->getRow($b)) {
        // Not a true loop
        echo("Row $a " . $stoppedRocks->getRow($a) . "\n");
        echo("Row $b " . $stoppedRocks->getRow($b) . "\n");
        break;
    }
}

// Need to figure out what the height would be at 1,000,000,000,000

$tmp = (1000000000000 - $offset);
$loopOffset = $tmp % $loopSize;
$loopCount = floor($tmp / $loopSize);
echo("result would occur after $loopCount loops at offset $loopOffset\n");

// The height of a loop for all full loops.
// Then we need the height before looping starts + height of remaining loopOffset.
// This is the same as the height in original first loop.
$height = $loopCount * $loopHeight + $heights[$offset + $loopOffset];

$part2 = $height;
echo "Part 2: $part2\n";
