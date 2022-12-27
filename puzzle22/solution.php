<?php

include_once __DIR__ . "/../helpers/Grid.php";

$lines = file(dirname(__FILE__) . "/input");
//$lines = file(dirname(__FILE__) . "/sample");
$part1 = 0;
$part2 = 0;

$lines = array_map("rtrim", $lines);

function move(Grid $grid, int $value, int $facing, mixed $x, mixed $y): array {
    $dx = 0;
    $dy = 0;
    switch ($facing) {
        case 0:
            $dx=1;
            break;
        case 1:
            $dy=1;
            break;
        case 2:
            $dx=-1;
            break;
        case 3:
            $dy=-1;
            break;
    }

    for ($move = 0; $move < $value; $move++) {
        $dis = 0;
        // Wrap through any "whitespace"
        do {
            $dis++;
            $nx = ($x + $grid->getWidth() + $dx * $dis) % $grid->getWidth();
            $ny = ($y + $grid->getHeight() + $dy * $dis) % $grid->getHeight();
            $g = $grid->get($nx, $ny);
        } while ($g == " ");

        // Then check the resulting location for a wall.
        if ($g == "#") {
            // If we hit a wall stop moving.
            return [$x, $y];
        }
        // Update location and repeat
        $x = $nx;
        $y = $ny;
    }
    return [$x, $y];
}

$lastLine = array_pop($lines);
$grid = Grid::createFromLines($lines);
$grid->print();

$y = 0;
for ($x = 0; $x < $grid->getWidth(); $x++) {
    if ($grid->get($x, $y) == ".") {
        break;
    }
}

// R,D,L,U = 0,1,2,3
$facings = [">","v",">","^"];
// Start facing right
$facing = 0;
echo("Start at $x, $y, facing $facing\n");

$value = 0;

for ($i = 0; $i < strlen($lastLine); $i++) {
    if ($lastLine[$i] == "R") {
        [$x, $y] = move($grid, $value, $facing, $x, $y);
        // turn right?
        $facing++;
        if ($facing >= 4) {
            $facing -= 4;
        }
        $value = 0;
    } elseif ($lastLine[$i] == "L") {
        [$x, $y] = move($grid, $value, $facing, $x, $y);
        // turn left;
        $facing--;
        if ($facing < 0) {
            $facing += 4;
        }
        $value = 0;
    } else {
         $value = $value * 10 + intval($lastLine[$i]);
    }
}
[$x, $y] = move($grid, $value, $facing, $x, $y);
echo($value . " (no turn)\n");

echo "End at $x, $y, facing $facing\n";

$part1 = ($y + 1) * 1000 + ($x + 1) * 4 + $facing;
echo "Part 1: $part1\n";

// TODO wrap as a cube.
echo "Part 2: $part2\n";
