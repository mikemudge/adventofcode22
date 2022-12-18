<?php

$lines = file(dirname(__FILE__) . "/input");
//$lines = file(dirname(__FILE__) . "/sample");
$part1 = 0;
$part2 = 0;

$lines = array_map("trim", $lines);

function compare($a, $b) {
    // checkOrder?
    if (is_array($a)) {
        if (!is_array($b)) {
            $b = [$b];
        }
        for ($i = 0; $i < count($a); $i++) {
            if ($i >= count($b)) {
                // Right ran out
                return false;
            }
            $inorder = compare($a[$i], $b[$i]);
            if ($inorder !== null) {
                return $inorder;
            }
        }
        if (count($b) > count($a)) {
            // Left ran out.
            return true;
        }
    } else {
        // a is an int.
        if (is_array($b)) {
            return compare([$a], $b);
        }
        if ($a < $b) {
            return true;
        }
        if ($a > $b) {
            return false;
        }
        return null;
    }
}

$divider1 = [[2]];
$divider2 = [[6]];
$divider1Idx = 1;
// Start at idx 2 because divider1Idx is before this packet.
$divider2Idx = 2;
for($i = 0; $i < count($lines); $i += 3) {
    $a = json_decode($lines[$i]);
    $b = json_decode($lines[$i + 1]);
    $inorder = compare($a, $b);
    $pair = $i / 3 + 1;
    if ($inorder) {
        echo("Pair $pair is in order\n");
        $part1 += $pair;
    } else if ($inorder === false) {
        echo("Pair $pair is out of order\n");
    } else {
        echo("Pair $pair is unknown\n");
    }
    $inorder = compare($a, $divider1);
    if ($inorder) {
        $divider1Idx++;
    }
    $inorder = compare($b, $divider1);
    if ($inorder) {
        $divider1Idx++;
    }
    $inorder = compare($a, $divider2);
    if ($inorder) {
        $divider2Idx++;
    }
    $inorder = compare($b, $divider2);
    if ($inorder) {
        $divider2Idx++;
    }
}
echo "Divider indexes: $divider1Idx, $divider2Idx\n";
//Divider indexes: 110, 198
//Part 2: 21780

$part2 = $divider1Idx * $divider2Idx;

echo "Part 1: $part1\n";
// 21780 is too low (because divider2 didn't consider divider1 being before it in the order).
echo "Part 2: $part2\n";
