<?php

include_once __DIR__ . "/../helpers/Grid.php";

$lines = file(dirname(__FILE__) . "/input"); $size = 50;
$lines = file(dirname(__FILE__) . "/sample"); $size = 4;
global $size;
$part1 = 0;
$part2 = 0;

$lines = array_map("rtrim", $lines);

$grid = Grid::createFromLines($lines);
$grid->print();
echo($grid->getWidth() . "," . $grid->getHeight() . "\n");

// Need my grid to be larger/growable.

// Need to iterate elves movements one round at a time.


echo "Part 1: $part1\n";
echo "Part 2: $part2\n";
