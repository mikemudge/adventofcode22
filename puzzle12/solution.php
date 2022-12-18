<?php

include_once __DIR__ . "/../helpers/Grid.php";

$lines = file(dirname(__FILE__) . "/input");
//$lines = file(dirname(__FILE__) . "/sample");

$lines = array_map("trim", $lines);

$grid = Grid::createFromLines($lines);
$start = $grid->find("S");
$end = $grid->find("E");
$grid->print();
echo "Start " . json_encode($start) . PHP_EOL;
echo "End " . json_encode($end) . PHP_EOL;


function bfs(array $start, array $end, Grid $grid) {
    // Search algorithm to find shortest path from start -> end
    $queue = [];
    $visited = [];
    $queue[] = [$start[0], $start[1], 0, 'a'];

    while (!empty($queue)) {
        [$x, $y, $dis, $prevHeight] = array_shift($queue);
        $height = $grid->get($x, $y);
        if ($height == 'S') {
            $height = 'a';
        }
        if ($height == 'E') {
            $height = 'z';
        }
        if ($height == null) {
            // Invalid location
            continue;
        }
        if (ord($height) > ord($prevHeight) + 1) {
            // The height step was too much.
            continue;
        }
        if ($x == $end[0] && $y == $end[1]) {
            echo("Goal at $dis" . PHP_EOL);
            // Found the goal
            return $dis;
        }
        if (array_key_exists("$x-$y", $visited)) {
            // Already been to this square.
            continue;
        }
        $visited["$x-$y"] = true;

        // Try all directions.
        $queue[] = [$x + 1, $y, $dis + 1, $height];
        $queue[] = [$x - 1, $y, $dis + 1, $height];
        $queue[] = [$x, $y + 1, $dis + 1, $height];
        $queue[] = [$x, $y - 1, $dis + 1, $height];
    }
}

$part1 = 0;
$part2 = 0;

$part1 = bfs($start, $end, $grid);

$part2 = 10000000;
// The brute force approach.
// TODO an optimization could have done a reverse search from the end to find any 'a'.
for($y = 0; $y < $grid->getHeight(); $y++) {
    for ($x = 0; $x < $grid->getWidth(); $x++) {
        if ($grid->get($x, $y) == 'a') {
            $pos = bfs([$x, $y], $end, $grid);
            // It may not be possible to reach the end from every 'a'.
            if ($pos) {
                echo $x . ", " . $y . " = " . $pos . PHP_EOL;
                $part2 = min($pos, $part2);
            }
        }
    }
}

echo "Part 1: $part1\n";
echo "Part 2: $part2\n";
