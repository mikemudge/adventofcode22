<?php

include_once __DIR__ . "/../helpers/Grid.php";

$lines = file(dirname(__FILE__) . "/input");
//$lines = file(dirname(__FILE__) . "/sample");
$part1 = 0;
$part2 = 0;

$lines = array_map("trim", $lines);

$bottom = 0;
$paths = [];
for($i = 0; $i < count($lines); $i++) {
    $pairs = explode(" -> ", $lines[$i]);
    foreach($pairs as $pair) {
        [$x, $y] = explode(",", $pair);
        $x = intval($x);
        $y = intval($y);
        $bottom = max($bottom, $y);
        $path[] = [$x, $y];
    }
    $paths[] = $path;
    $path = [];
}

echo("Bottom starts at $bottom\n");

$grid = new Grid(1000, $bottom + 2, ".");

foreach ($paths as $path) {
    [$px, $py] = $path[0];
    $bottom = max($bottom, $py);
    for ($i = 1; $i < count($path); $i++) {
        [$x, $y] = $path[$i];
        if ($x == $px) {
            // line
            for ($y1 = min($y, $py); $y1 <= max($y, $py); $y1++) {
                $grid->set($x, $y1, "#");
            }
        } else if($y == $py) {
            // line
            for ($x1 = min($x, $px); $x1 <= max($x, $px); $x1++) {
                $grid->set($x1, $y, "#");
            }
        } else {
            throw new RuntimeException("Line was not straight, can't draw it");
        }
        $px = $x;
        $py = $y;
    }
}
$grid->print(450, 0, 100);

$part1complete = false;
while(true) {
    // Drop sand, find rest location.
    $rx = 500;
    $ry = 0;
    $falling = true;
    while($falling) {
        $below = $grid->get($rx, $ry + 1);
        $left = $grid->get($rx - 1, $ry + 1);
        $right = $grid->get($rx + 1, $ry + 1);
        if ($below === ".") {
            $ry++;
        } else if ($left === ".") {
            $rx--;
            $ry++;
        } else if ($right === ".") {
            $rx++;
            $ry++;
        } else {
            // Came to rest.
            $falling = false;
        }
    }
    if ($ry >= $bottom) {
        // Falling forever (aka landing on the bottom) means we are all done.
        $part1complete = true;
    }
    if (!$part1complete) {
        $part1++;
    }
    $part2++;
    $grid->set($rx, $ry, "o");
    if ($part2 % 500 == 0) {
        echo("After $part2 sands drop\n");
        $grid->print(450, 0, 100);
    }
    if ($rx === 500 && $ry === 0) {
        // sand has clogged the input grid location so we can stop spawning more.
        break;
    };
}
echo("After $part2 sands drop\n");
$grid->print(450, 0, 100);

echo "Part 1: $part1\n";
echo "Part 2: $part2\n";
