<?php

$lines = file(dirname(__FILE__) . "/input");
//$lines = file(dirname(__FILE__) . "/sample");
$part1 = 0;
$part2 = 0;

$lines = array_map("trim", $lines);

$cubes = [];
// lava is #, outside is . and air pockets are o.
$grid3d = [];
$size = 0;
foreach ($lines as $line) {
    [$x, $y, $z] = array_map("intval", explode(",", $line));
    $cubes[] = [$x, $y, $z];
    $grid3d[$z][$y][$x] = "#";
    $size = max([$x, $y, $z, $size]);
}

// Because the locations are 0 based.
$size++;
for ($z = 0; $z < $size; $z++) {
    for ($y = 0; $y < $size; $y++) {
        for ($x = 0; $x < $size; $x++) {
            if (!isset($grid3d[$z][$y][$x])) {
                $grid3d[$z][$y][$x] = "o";
            }
        }
    }
}

function check($grid3d, int $x, int $y, int $z) {
    if (isset($grid3d[$z][$y][$x]) && $grid3d[$z][$y][$x] == "#") {
        // This is not an exposed edge.
        return 0;
    }
    return 1;
}

foreach ($cubes as $cube) {
    [$x, $y, $z] = $cube;
    for ($i = -1; $i <= 1; $i+=2) {
        $part1 += check($grid3d, $x + $i, $y, $z);
        $part1 += check($grid3d, $x, $y + $i, $z);
        $part1 += check($grid3d, $x, $y, $z + $i);
    }
}

echo "Part 1: $part1\n";

$next = [];
// Now find all the externally reachable places.
for ($i = 0; $i < $size; $i++) {
    for ($ii = 0; $ii < $size; $ii++) {
        if ($grid3d[0][$i][$ii] != "#") {
            // This location is reachable.
            $grid3d[0][$i][$ii] = ".";
            $next[] = [0, $i, $ii];
        }
        if ($grid3d[$i][0][$ii] != "#") {
            // This location is reachable.
            $grid3d[$i][0][$ii] = ".";
            $next[] = [$i, 0, $ii];
        }
        if ($grid3d[$i][$ii][0] != "#") {
            // This location is reachable.
            $grid3d[$i][$ii][0] = ".";
            $next[] = [$i, $ii, 0];
        }
    }
}

while(!empty($next)) {
    $tmp = $next;
    $next = [];
    foreach ($tmp as $loc) {
        [$x, $y, $z] = $loc;
        for ($i = -1; $i <= 1; $i+=2) {
            if ($x+$i >= 0 && $x+$i < $size && $grid3d[$x + $i][$y][$z] == "o") {
                // This location is reachable.
                $grid3d[$x + $i][$y][$z] = ".";
                $next[] = [$x + $i, $y, $z];
            }
            if ($y+$i >= 0 && $y+$i < $size && $grid3d[$x][$y + $i][$z] == "o") {
                // This location is reachable.
                $grid3d[$x][$y + $i][$z] = ".";
                $next[] = [$x, $y + $i, $z];
            }
            if ($z+$i >= 0 && $z+$i < $size && $grid3d[$x][$y][$z + $i] == "o") {
                // This location is reachable.
                $grid3d[$x][$y][$z + $i] = ".";
                $next[] = [$x, $y, $z + $i];
            }
        }
    }
}

function check2($grid3d, int $x, int $y, int $z) {
    if (!isset($grid3d[$z][$y][$x]) || $grid3d[$z][$y][$x] === ".") {
        return 1;
    }
    // This is not an exposed edge.
    return 0;
}

foreach ($cubes as $cube) {
    [$x, $y, $z] = $cube;
    for ($i = -1; $i <= 1; $i+=2) {
        $part2 += check2($grid3d, $x + $i, $y, $z);
        $part2 += check2($grid3d, $x, $y + $i, $z);
        $part2 += check2($grid3d, $x, $y, $z + $i);
    }
}

// This shows the cube one layer at a time.
//for ($z = 0; $z < $size; $z++) {
//    echo("z=$z\n");
//    for ($y = 0; $y < $size; $y++) {
//        print(join("", $grid3d[$z][$y]) . "\n");
//    }
//}

// 2497 was too low.
// I missed counting the lava cubes on the edge of the grid with externally facing surfaces.
echo "Part 2: $part2\n";
