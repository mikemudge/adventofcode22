<?php

include_once __DIR__ . "/../helpers/Terminal.php";

$lines = file(dirname(__FILE__) . "/input");

$lines = array_map("trim", $lines);

$part1 = 0;
$part2 = 0;

$grid1 = [];
$grid2 = [];
for ($y = 0; $y < 1000; $y++) {
    $grid1[] = [];
    $grid2[] = [];
    for ($x = 0; $x < 1000; $x++) {
        $grid1[$y][$x] = ".";
        $grid2[$y][$x] = ".";
    }
}
echo("Grid = " . count($grid1) . "," .count($grid1[0]) . "\n");

for ($i = 0; $i < 10; $i++) {
    $body[$i] = [
        'x' => 500,
        'y' => 500
    ];
}
$grid1[$body[1]['y']][$body[1]['x']] = "s";
$grid2[$body[9]['y']][$body[9]['x']] = "s";

foreach ($lines as $line) {
    [$dir, $dis] = explode(" ", trim($line));
    echo("$dir $dis");
    for ($mv = 0; $mv < $dis; $mv++) {
        switch ($dir) {
            case "L":
                $body[0]["x"]--;
                break;
            case "R":
                $body[0]["x"]++;
                break;
            case "U":
                $body[0]["y"]--;
                break;
            case "D":
                $body[0]["y"]++;
                break;
            default:
                throw new Exception("Unknown dir $dir");
        }
        for ($i = 1; $i < count($body); $i++) {
            if ($body[$i - 1]['x'] < $body[$i]['x'] - 1) {
                $body[$i]['x']--;
                if ($body[$i]['y'] > $body[$i - 1]['y']) {
                    $body[$i]['y']--;
                } else if ($body[$i]['y'] < $body[$i - 1]['y']) {
                    $body[$i]['y']++;
                }
            }
            if ($body[$i - 1]['x'] > $body[$i]['x'] + 1) {
                $body[$i]['x']++;
                if ($body[$i]['y'] > $body[$i - 1]['y']) {
                    $body[$i]['y']--;
                } else if ($body[$i]['y'] < $body[$i - 1]['y']) {
                    $body[$i]['y']++;
                }
            }
            if ($body[$i - 1]['y'] < $body[$i]['y'] - 1) {
                $body[$i]['y']--;
                if ($body[$i]['x'] > $body[$i - 1]['x']) {
                    $body[$i]['x']--;
                } else if ($body[$i]['x'] < $body[$i - 1]['x']) {
                    $body[$i]['x']++;
                }
            }
            if ($body[$i - 1]['y'] > $body[$i]['y'] + 1) {
                $body[$i]['y']++;
                if ($body[$i]['x'] > $body[$i - 1]['x']) {
                    $body[$i]['x']--;
                } else if ($body[$i]['x'] < $body[$i - 1]['x']) {
                    $body[$i]['x']++;
                }
            }
        }

        $grid1[$body[1]['y']][$body[1]['x']] = "#";
        $grid2[$body[9]['y']][$body[9]['x']] = "#";
    }
}
function printGrid(array $grid) {
    foreach ($grid as $row) {
        echo(implode("", $row) . "\n");
    }
}

printGrid($grid1);

for ($y = 0; $y < 1000; $y++) {
    for ($x = 0; $x < 1000; $x++) {
        if ($grid1[$y][$x] != ".") {
            $part1++;
        }
    }
}

for ($y = 0; $y < 1000; $y++) {
    for ($x = 0; $x < 1000; $x++) {
        if ($grid2[$y][$x] != ".") {
            $part2++;
        }
    }
}

echo "Part 1: $part1\n";
// 2592 is too high.
echo "Part 2: $part2\n";
