<?php

include_once __DIR__ . "/../helpers/Terminal.php";

$lines = file(dirname(__FILE__) . "/input");

$lines = array_map("trim", $lines);

$part1 = 0;
$part2 = 0;

$term = new Terminal();
$term->evaluate($lines);

//$term->getRoot()->print("  ", 0);

$allDirs = $term->getRoot()->getAllSubDirs();
foreach($allDirs as $dir) {
    if ($dir->getSize() <= 100000) {
        $part1 += $dir->getSize();
    }
}
$totalSize = $term->getRoot()->getSize();
echo "Total Size " . $totalSize . "\n";
// This needs to be below 40M to fit our update.
$remove = $totalSize - 40000000;
echo "Need to remove " . $remove . "\n";

$part2 = $totalSize;
foreach($allDirs as $dir) {
    if ($dir->getSize() > $remove) {
        // This dir is eligible for removing.
        $part2 = min($dir->getSize(), $part2);
    }
}

echo "Part 1: $part1\n";
echo "Part 2: $part2\n";
