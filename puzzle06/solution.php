<?php

$lines = file(dirname(__FILE__) . "/input");

$part1 = 0;
$part2 = 0;
$line = str_split(trim($lines[0]));

// Sample
// $line = str_split("bvwbjplbgvbhsrlpgdmjqwftvncz");

for ($i = 0; $i < count($line) - 4; $i++) {
    $code = array_unique(array_slice($line, $i, 4));
    if ($part1 == 0 && count($code) == 4) {
        echo("Code ". join($code) . " has 4 chars\n");
        $part1 = $i + 4;
    }
    $code2 = array_unique(array_slice($line, $i, 14));
    if ($part2 == 0 && count($code2) == 14) {
        echo("Code ". join($code2) . " has 14 chars\n");
        $part2 = $i + 14;
    }
}

//1079 is too low
echo "Part 1: $part1\n";
echo "Part 2: $part2\n";

