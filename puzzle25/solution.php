<?php

include_once __DIR__ . "/../helpers/Grid.php";

$lines = file(dirname(__FILE__) . "/input");
//$lines = file(dirname(__FILE__) . "/sample");
$part1 = 0;
$part2 = 0;

$lines = array_map("rtrim", $lines);
$vals = [
    '2' => 2,
    '1' => 1,
    '0' => 0,
    '-' => -1,
    '=' => -2
];
$sum = 0;
foreach ($lines as $l => $line) {
    $data = str_split($line);
    $value = 0;
    for ($i = 0; $i < count($data); $i++) {
        $value *= 5;
        $v = $vals[$data[$i]];
        $value += $v;
    }
    $sum += $value;
    echo("Line $l $line = $value\n");
}

echo "Sum: $sum\n";

// Convert to SNAFU?
$base5 = base_convert($sum, 10, 5);
echo "Base5: $base5\n";
$data = array_reverse(str_split($base5));

$strVal = '';
$c = 0;
for ($i = 0; $i < count($data); $i++) {
    $v = intval($data[$i]) + $c;
    $c = 0;
    while ($v >= 3) {
        $c++;
        $v -= 5;
    }
    if ($v == -2) {
        $v = "=";
    } else if ($v == -1) {
        $v = "-";
    }
    $strVal = "$v$strVal";
}
if ($c) {
    $strVal = "$c$strVal";
}
$part1 = $strVal;

echo "Part 1: $part1\n";
echo "Part 2: $part2\n";
