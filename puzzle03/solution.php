<?php

$lines = file(dirname(__FILE__) . "/input");

$part1 = 0;
$part2 = 0;
foreach($lines as $line) {
    $line = trim($line);

    $part1 += solvePart1($line);
}

for($i = 0; $i < count($lines); $i+=3) {
    $part2 += solvePart2(trim($lines[$i]), trim($lines[$i + 1]), trim($lines[$i + 2]));
}

// 4118 is too low.
// 7940 is too high.
echo "Part 1: $part1\n";
echo "Part 2: $part2\n";

function solvePart1($line) {
    $idx = strlen($line) / 2;
    $compartment1 = substr($line, 0, $idx);
    $compartment2 = substr($line, $idx);

    $diff = array_values(array_intersect(str_split($compartment1), str_split($compartment2)));

    return getPriority($diff[0]);
}


function solvePart2($line1, $line2, $line3) {

    $diff = array_values(array_intersect(str_split($line1), str_split($line2), str_split($line3)));

    return getPriority($diff[0]);
}

function getPriority($letter) {
    $c = ord($letter);
    if ($c > 96) {
        // a-z
        $priority = $c - 96;
    } else {
        // A-Z
        $priority = $c - 64 + 26;
    }
    echo($letter . ": " . $priority . "\n");

    return $priority;
}
