<?php

$lines = file(dirname(__FILE__) . "/input");

$part1 = 0;
$part2 = 0;
foreach($lines as $line) {
    $line = trim($line);

    $part1 += solvePart1($line);
    $part2 += solvePart2($line);
}


echo "Part 1: $part1\n";
// 696 is too low.
echo "Part 2: $part2\n";

function solvePart1($line) {
    [$elf1, $elf2] = explode(",", $line);
    [$l1, $r1] = explode("-", $elf1);
    [$l2, $r2] = explode("-", $elf2);
    $l1 = intval($l1);
    $l2 = intval($l2);
    $r1 = intval($r1);
    $r2 = intval($r2);

    if ($l1 <= $l2 && $r1 >= $r2) {
        return 1;
    }
    if ($l1 >= $l2 && $r1 <= $r2) {
        return 1;
    }
    return 0;
}


function solvePart2($line) {
    [$elf1, $elf2] = explode(",", $line);
    [$l1, $r1] = explode("-", $elf1);
    [$l2, $r2] = explode("-", $elf2);
    $l1 = intval($l1);
    $l2 = intval($l2);
    $r1 = intval($r1);
    $r2 = intval($r2);

    if ($l1 <= $r2 && $l2 <= $r1) {
        return 1;
    }
    return 0;
}
