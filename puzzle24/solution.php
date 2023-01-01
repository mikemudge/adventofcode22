<?php

include_once __DIR__ . "/../helpers/Grid.php";

$lines = file(dirname(__FILE__) . "/input");
//$lines = file(dirname(__FILE__) . "/sample");
$part1 = 0;
$part2 = 0;

$lines = array_map("rtrim", $lines);

$height = count($lines) - 2;
$width = strlen($lines[0]) - 2;
$left = new Grid($width, $height, 0);
$right = new Grid($width, $height, 0);
$up = new Grid($width, $height, 0);
$down = new Grid($width, $height, 0);
// Find start/end?
for($y = 0; $y < $height; $y++) {
    $line = $lines[$y + 1];
    for ($x = 0; $x < $width; $x++) {
        $v = $line[$x + 1];
        if ($v == "<") {
            $left->set($x, $y, 1);
        }
        if ($v == ">") {
            $right->set($x, $y, 1);
        }
        if ($v == "^") {
            $up->set($x, $y, 1);
        }
        if ($v == "v") {
            $down->set($x, $y, 1);
        }
    }
}

function blizzard(int $x, int $y, int $round) {
    global $width;
    global $height;
    global $left;
    global $right;
    global $up;
    global $down;

    if ($x < 0 || $x >= $width) {
        return false;
    }
    if ($y < 0 || $y >= $height) {
        return false;
    }
    $result = '';
    if ($left->get(($x + $round) % $width, $y)) {
        $result .= "<";
    }
    if ($up->get($x, ($y + $round) % $height)) {
        $result .= "^";
    }
    if ($right->get((($x - $round) % $width + $width) % $width, $y)) {
        $result .= ">";
    }
    if ($down->get($x, (($y - $round) % $height + $height) % $height)) {
        $result .= "v";
    }
    if (!$result) {
        $result = '.';
    }
    if (strlen($result) > 1) {
        $result = strval(strlen($result));
    }
    return $result;
}

for ($round = 0; $round <= 10; $round++) {
    echo("Round $round\n");
    for($y = 0; $y < $height; $y++) {
        for ($x = 0; $x < $width; $x++) {
            echo(blizzard($x, $y, $round));
        }
        echo("\n");
    }
}

$visited = [];
$gx = $width - 1;
$gy = $height;
$state = [0, -1, 0];
$pq = new SplPriorityQueue();
$pq->insert($state, 0);
$queueReads = 0;
while($pq->valid()) {
    $state = $pq->extract();
    $queueReads++;
    [$x, $y, $t] = $state;
    $key = "$x-$y-$t";
    if (array_key_exists($key, $visited)) {
        // Already been to this state.
        continue;
    }
    $visited[$key] = true;
    if ($queueReads % 10000 == 0) {
    echo("At $queueReads iterations, " . ($t + $disToGoal) . " $x, $y, $t, $disToGoal\n");
    }
    if ($x == $gx && $y == $gy) {
        // winner
        $part1 = $t;
        break;
    }
    // Check if this state is valid?

    if ($x == 0 && $y == -1) {
        // start state is ok/safe?
    } else if (blizzard($x, $y, $t) !== ".") {
        // This state is not accessible.
        continue;
    }

    // Try all neighbours
    $disToGoal = ($gx - $x) + ($gy - $y);
    $pq->insert([$x + 1, $y, $t + 1], 1000000 - $t - $disToGoal + 1);
    $pq->insert([$x - 1, $y, $t + 1], 1000000 - $t - $disToGoal - 1);
    $pq->insert([$x, $y + 1, $t + 1], 1000000 - $t - $disToGoal + 1);
    $pq->insert([$x, $y - 1, $t + 1], 1000000 - $t - $disToGoal - 1);
    // Staying still is ok too.
    $pq->insert([$x, $y, $t + 1], 1000000 - $t - $disToGoal);
}
echo("Checked $queueReads states and found ?\n");
echo "Part 1: $part1\n";


$visited = [];
$state = [$gx, $gy, $part1];
$gx = 0;
$gy = -1;
$pq = new SplPriorityQueue();
$pq->insert($state, 0);
$queueReads = 0;
while($pq->valid()) {
    $state = $pq->extract();
    $queueReads++;
    [$x, $y, $t] = $state;
    $key = "$x-$y-$t";
    if (array_key_exists($key, $visited)) {
        // Already been to this state.
        continue;
    }
    $visited[$key] = true;
    if ($queueReads % 10000 == 0) {
        echo("At $queueReads iterations, " . ($t + $disToGoal) . " $x, $y, $t, $disToGoal\n");
    }
    if ($x == $gx && $y == $gy) {
        // winner
        $backToStart = $t;
        break;
    }
    // Check if this state is valid?

    if ($x == $width - 1 && $y == $height) {
        // end state is ok/safe?
    } else if (blizzard($x, $y, $t) !== ".") {
        // This state is not accessible.
        continue;
    }

    // Try all neighbours
    $disToGoal = ($gx - $x) + ($gy - $y);
    $pq->insert([$x + 1, $y, $t + 1], 1000000 - $t - $disToGoal + 1);
    $pq->insert([$x - 1, $y, $t + 1], 1000000 - $t - $disToGoal - 1);
    $pq->insert([$x, $y + 1, $t + 1], 1000000 - $t - $disToGoal + 1);
    $pq->insert([$x, $y - 1, $t + 1], 1000000 - $t - $disToGoal - 1);
    // Staying still is ok too.
    $pq->insert([$x, $y, $t + 1], 1000000 - $t - $disToGoal);
}
echo("Checked $queueReads states and found ?\n");

echo "reached start again at $backToStart\n";

// one more time.

$visited = [];
$gx = $width - 1;
$gy = $height;
$state = [0, -1, $backToStart];
$pq = new SplPriorityQueue();
$pq->insert($state, 0);
$queueReads = 0;
while($pq->valid()) {
    $state = $pq->extract();
    $queueReads++;
    [$x, $y, $t] = $state;
    $key = "$x-$y-$t";
    if (array_key_exists($key, $visited)) {
        // Already been to this state.
        continue;
    }
    $visited[$key] = true;
    if ($queueReads % 10000 == 0) {
        echo("At $queueReads iterations, " . ($t + $disToGoal) . " $x, $y, $t, $disToGoal\n");
    }
    if ($x == $gx && $y == $gy) {
        // winner
        $part2 = $t;
        break;
    }
    // Check if this state is valid?

    if ($x == 0 && $y == -1) {
        // start state is ok/safe?
    } else if (blizzard($x, $y, $t) !== ".") {
        // This state is not accessible.
        continue;
    }

    // Try all neighbours
    $disToGoal = ($gx - $x) + ($gy - $y);
    $pq->insert([$x + 1, $y, $t + 1], 1000000 - $t - $disToGoal + 1);
    $pq->insert([$x - 1, $y, $t + 1], 1000000 - $t - $disToGoal - 1);
    $pq->insert([$x, $y + 1, $t + 1], 1000000 - $t - $disToGoal + 1);
    $pq->insert([$x, $y - 1, $t + 1], 1000000 - $t - $disToGoal - 1);
    // Staying still is ok too.
    $pq->insert([$x, $y, $t + 1], 1000000 - $t - $disToGoal);
}
echo("Checked $queueReads states and found ?\n");

echo "Part 2: $part2\n";
