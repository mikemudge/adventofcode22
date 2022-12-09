<?php

$lines = file(dirname(__FILE__) . "/input");

$part1 = 0;
$part2 = 0;
$stacks = [];
$stacks[0] = null;
$stacks[1] = ["B", "Q", "C"];
$stacks[2] = ["R", "Q", "W", "Z"];
$stacks[3] = ["B", "M", "R", "L", "V"];
$stacks[4] = ["C", "Z", "H", "V", "T", "W"];
$stacks[5] = ["D", "Z", "H", "B", "N", "V", "G"];
$stacks[6] = ["H", "N", "P", "C", "J", "F", "V", "Q"];
$stacks[7] = ["D", "G", "T", "R", "W", "Z", "S"];
$stacks[8] = ["C", "G", "M", "N", "B", "W", "Z", "P"];
$stacks[9] = ["N", "J", "B", "M", "W", "Q", "F", "P"];

for ($i = 0; $i <= 9;$i++) {
    $part1stacks[] = $stacks[$i];
    $part2stacks[] = $stacks[$i];
}
$section = 1;
foreach($lines as $line) {
    $line = trim($line);
    if (empty($line)) {
        $section = 2;
        continue;
    }
    if ($section == 1) {
        continue;
    }

    [$move, $cnt, $from, $src, $to, $dest] = explode(" ", $line);

    $cnt = intval($cnt);
    $src = intval($src);
    $dest = intval($dest);
    $tmp = [];
    for ($i = 0; $i < $cnt; $i++) {
        $part1stacks[$dest][] = array_pop($part1stacks[$src]);
        $tmp[] = array_pop($part2stacks[$src]);
    }
    for ($i = 0; $i < $cnt; $i++) {
        $part2stacks[$dest][] = array_pop($tmp);
    }
}

$part1 = "";
for ($i = 1; $i <= 9;$i++) {
    $part1 .= array_pop($part1stacks[$i]);
}
$part2 = "";
for ($i = 1; $i <= 9;$i++) {
    $part2 .= array_pop($part2stacks[$i]);
}

echo "Part 1: $part1\n";
echo "Part 2: $part2\n";

