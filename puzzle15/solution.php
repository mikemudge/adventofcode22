<?php

$lines = file(dirname(__FILE__) . "/input"); $testY = 2000000;
//$lines = file(dirname(__FILE__) . "/sample"); $testY = 10;
$part1 = 0;
$part2 = 0;

$lines = array_map("trim", $lines);

class Sensor {
    private int $x;

    /**
     * @return int
     */
    public function getX(): int {
        return $this->x;
    }

    /**
     * @return int
     */
    public function getY(): int {
        return $this->y;
    }

    /**
     * @return int
     */
    public function getRange(): int {
        return $this->range;
    }

    /**
     * @return array
     */
    public function getBeacon(): array {
        return $this->beacon;
    }
    private int $y;
    private int $range;
    private array $beacon;

    public function __construct($x, $y, $beacon) {
        $this->x = $x;
        $this->y = $y;
        $this->beacon = $beacon;
        [$bx, $by] = $beacon;
        $this->range = abs($x - $bx) + abs($y - $by);
    }

    public function findNoBeaconRange(int $y) {
        // We use up some of our range just getting to the y space.
        $dis = $this->range - abs($this->y - $y);
        if ($dis < 0) {
            // We don't have any coverage of $y
            return null;
        }
        // The rest is covering the y space.
        return [$this->x - $dis, $this->x + $dis];
    }

    public function findNoBeaconRangeX(int $x) {
        // We use up some of our range just getting to the y space.
        $dis = $this->range - abs($this->x - $x);
        if ($dis < 0) {
            // We don't have any coverage of $y
            return null;
        }
        // The rest is covering the y space.
        return [$this->y - $dis, $this->y + $dis];
    }

    public function inRange(int $x, int $y): bool {
        $dis = abs($this->y - $y) + abs($this->x - $x);
        return $dis <= $this->range;
    }
}

function calculateRangeCoverage($ranges) {
    // Sort the ranges so we know the next range is always >= the current one.
    sort($ranges);
    $cover = 0;
    $last = $ranges[0];
    // $x represents how far we have checked so far.
    $x = $last[0];
    // $x2 represents how far we are definitely covered too.
    $x2 = $last[1];
    for ($i = 1; $i < count($ranges); $i++) {
        [$l, $r] = $ranges[$i];
        // We want to move forward, how far can we move?
        if ($x2 < $l) {
            // no overlap between ranges, move to next range.
            $cover += $x2 - $x;
            $x = $l;
            $x2 = $r;
        } else {
            // There is overlap between x->x2 and l->r
            $cover += $l - $x;
            $x = $l;
            $x2 = max($x2, $r);
        }
    }
// The final range needs to be added as well.
    $cover += $x2 - $x;
    return $cover;
}

/** @var Sensor[] $sensors */
$sensors = [];
for($i = 0; $i < count($lines); $i++) {
    $line = $lines[$i];
    $parts = explode(": closest beacon is at ", $line);
    $sensor = explode(", ", substr($parts[0], 10));
    $sx = intval(substr($sensor[0], 2));
    $sy = intval(substr($sensor[1], 2));
    $beacon = explode(", ", $parts[1]);
    $bx = intval(substr($beacon[0], 2));
    $by = intval(substr($beacon[1], 2));
    $sensors[] = new Sensor($sx, $sy, [$bx, $by]);
    echo("$line\n");
}

foreach($sensors as $s) {
    echo($s->getX() . "," . $s->getY() . "@" . $s->getRange() . PHP_EOL);
}

// Build a range grid to store what regions are not containing beacons.
$ranges = [];
foreach($sensors as $s) {
    $range = $s->findNoBeaconRange($testY);
    if ($range === null) {
        continue;
    }
    // combine ranges?
    $ranges[] = $range;
}

$part1 = calculateRangeCoverage($ranges);

$ranges = [];
foreach($sensors as $s) {
    $range = $s->findNoBeaconRangeX(3120101);
    if ($range === null) {
        continue;
    }
    // combine ranges?
    if ($range[1] < 0) {
        continue;
    }
    $range[0] = max(0, $range[0]);
    if ($range[0] > 4000000) {
        continue;
    }
    $range[1] = min(4000000, $range[1]);
    $ranges[] = $range;
}
$part2 = calculateRangeCoverage($ranges);

$diamonds = [];
$allXs = [];
$allYs = [];
foreach($sensors as $s) {
    $x = $s->getX();
    $y = $s->getY();
    $size = $s->getRange();
    $diamonds[] = [$x + $y - $size, $y - $x - $size, $size * 2, $size * 2];
    $allXs[] = $x + $y - $size;
    $allXs[] = $x + $y + $size;
    $allYs[] = $y - $x - $size;
    $allYs[] = $y - $x + $size;
}

sort($allXs);
sort($allYs);

$lastX = $allXs[0];
$lastY = $allYs[0];
for ($i = 1; $i < count($allXs); $i++) {
    $x = $allXs[$i];
    $y = $allYs[$i];
    echo("x = $lastX -> $x with size " . ($x - $lastX) . PHP_EOL);
    echo("y = $lastY -> $y with size " . ($y - $lastY) . PHP_EOL);
    $lastX = $x;
    $lastY = $y;
}

// Ugly manual solve by finding the 1x1 gap in diamond space and reversing it to original space.
// 5754350 = y + x
// -485852 = y - x
// y = x - 485852
// 5754350 = x - 485852 + x
// 5754350 + 485852 = 2x
// x = 3120101
// y = 3120101 - 485852
// y = 2634249

$x = 3120101;
$y = 2634249;

// confirm it.
foreach ($sensors as $s) {
    if ($s->inRange($x, $y)) {
        // This should never be reached if x,y is right.
        echo("Is in range\n");
    }
}

$part2 = $x * 4000000 + $y;

// A diamond grid?
// Combined with a range collapsing grid?
// E.g 0-10, 11-1000, 1001-6000 would only have 3 columns of data instead of 6000.
// Number of ranges would be number of sensors * 2.

// Diamond grid is a Euclidean plane with transformed cartesian coordinates.
// x = y + x;
// y = y - x;

/*
Example grid showing y coordinates.
0,-1,-2,-3
1, 0,-1,-2
2, 1, 0,-1
3, 2, 1, 0
Example grid showing x coordinates
0,1,2,3
1,2,3,4
2,3,4,5
3,4,5,6


..#..
.###.
##B##
.###.
..#..

B = 2,2 in orig space.
B = 4,0 in dia space.
corner at 2,0 -> 2, -2
corner at 0,2 -> 2, 2
corner at 2,4 -> 6, 2
corner at 4,2 -> 6, -2
So our dia space is at 4,0
 */

echo "Part 1: $part1\n";
echo "Part 2: $part2\n";
