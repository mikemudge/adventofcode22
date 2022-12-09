<?php

include_once __DIR__ . "/../helpers/Terminal.php";

$lines = file(dirname(__FILE__) . "/input");

$lines = array_map("trim", $lines);

$part1 = 0;
$part2 = 0;

$grid = [];
foreach ($lines as $line) {
    $grid[] = array_map("intval", str_split(trim($line)));
}

echo("Grid = " . count($grid) . "," .count($grid[0]) . "\n");

for ($y = 0; $y < count($grid); $y++) {
    for ($x = 0; $x < count($grid[$y]); $x++) {
        $treeHeight = $grid[$y][$x];
        $totVisible = false;
        $totScore = 1;
        $visible = true;
        $score = 0;
        for ($i = $y + 1; $i < count($grid); $i++) {
            $score++;
            if ($grid[$i][$x] >= $treeHeight) {
                // not visible
                $visible = false;
                break;
            }
        }
        $totVisible |= $visible;
        $totScore *= $score;
        $visible = true;
        $score = 0;
        for ($i = $y - 1; $i >= 0; $i--) {
            $score++;
            if ($grid[$i][$x] >= $treeHeight) {
                // not visible
                $visible = false;
                break;
            }
        }
        $totVisible |= $visible;
        $totScore *= $score;
        $visible = true;
        $score = 0;
        for ($i = $x + 1; $i < count($grid[$y]); $i++) {
            $score++;
            if ($grid[$y][$i] >= $treeHeight) {
                // not visible
                $visible = false;
                break;
            }
        }
        $totVisible |= $visible;
        $totScore *= $score;
        $visible = true;
        $score = 0;
        for ($i = $x - 1; $i >= 0; $i--) {
            $score++;
            if ($grid[$y][$i] >= $treeHeight) {
                // not visible
                $visible = false;
                break;
            }
        }
        $totVisible |= $visible;
        $totScore *= $score;
        $part2 = max($totScore, $part2);
        if ($totVisible) {
            $part1++;
        }
    }
}

// 9801 is too high.
echo "Part 1: $part1\n";
// 19584 is too low.
echo "Part 2: $part2\n";
