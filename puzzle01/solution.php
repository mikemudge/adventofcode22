<?php

$lines = file(dirname(__FILE__) . "/input");

$sum = 0;
$best = 0;
foreach($lines as $line) {
    $line = trim($line);
    if (empty($line)) {
        if ($sum > 0) {
            $all[] = $sum;
        }
        $sum = 0;
        continue;
    }
    $sum += intval($line);
}
sort($all);
$best = $all[count($all) - 1];
$best3 = $all[count($all) - 1] + $all[count($all) - 2] + $all[count($all) - 3];

echo "Part 1: $best\n";
echo "Part 2: $best3\n";
