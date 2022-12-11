<?php

include_once __DIR__ . "/../helpers/Computer.php";

$lines = file(dirname(__FILE__) . "/input");
//$lines = file(dirname(__FILE__) . "/sample");

$lines = array_map("trim", $lines);

$part1 = 0;
$part2 = 0;

$monkeys = [];

foreach ($lines as $line) {
    if (empty($line)) {
        $monkeys[] = $monkey;
        $monkey = [];
        continue;
    }
    if (str_starts_with($line, "Starting items: ")) {
        $monkey['items'] = array_map('intval', explode(",", substr($line, 16)));
        continue;
    }
    if (str_starts_with($line, "Operation: ")) {
        // parse a math function, all functions are "num op num"
        // num can be "old" which means it uses the current value.
        // op can be + or * for add or multiply.
        $function = substr($line, 17);
        $bits = explode(" ", $function);
        $monkey['function'] = $bits;
        continue;
    }
    if (str_starts_with($line, "Test: ")) {
        // parse "divisible by X?
        $monkey['divider'] = intval(substr($line, 19));
        continue;
    }
    if (str_starts_with($line, "If true: ")) {
        $monkey['throwTrue'] = intval(substr($line, 25));
        continue;
    }
    if (str_starts_with($line, "If false: ")) {
        $monkey['throwFalse'] = intval(substr($line, 26));
    }
}
// Add the last monkey to the set.
$monkeys[] = $monkey;

echo json_encode($monkeys, JSON_PRETTY_PRINT);

function performFunction(array $function, int $item) {
    $lhs = $function[0];
    $op = $function[1];
    $rhs = $function[2];
    if ($lhs === 'old') {
        $lhs = $item;
    } else {
        $lhs = intval($lhs);
    }
    if ($rhs === 'old') {
        $rhs = $item;
    } else {
        $rhs = intval($rhs);
    }

    if ($op == "+") {
        return $lhs + $rhs;
    } else if ($op == "*") {
        return $lhs * $rhs;
    }
}

$inspections = array_fill(0, count($monkeys), 0);

$gcd = 1;
// Assuming these are all prime, which is true in my input.
for ($m = 0; $m < count($monkeys); $m++) {
    $gcd *= $monkeys[$m]['divider'];
}
echo "GCD is $gcd\n";

// In part 1 this was 20 rounds only.
for ($r =0; $r < 10000; $r++) {
    for ($m = 0; $m < count($monkeys); $m++) {
        $monkey = $monkeys[$m];
        for ($i = 0; $i < count($monkey['items']); $i++) {
            $inspections[$m]++;
            $item = $monkey['items'][$i];
            // Increase worry level
            $val = performFunction($monkey['function'], $item);
            // Decrease worry level
            $val = $val % $gcd;
            // In Part 1 this was
            // $val = floor($val / 3);

            // Test and hand off to right monkey.
            $test = $val % $monkey['divider'] == 0;
            $throw = $test ? $monkey['throwTrue'] : $monkey['throwFalse'];
            $monkeys[$throw]['items'][] = $val;
        }
        // The monkey threw all items away, so it should be empty now.
        $monkeys[$m]['items'] = [];
    }

    // Round end output.
    $round = ($r+1);
    if ($round % 1000 == 0) {
        echo("After round $round\n");
        for ($m = 0; $m < count($monkeys); $m++) {
            echo("Monkey $m: " . join(", ", $monkeys[$m]['items']) . "\n");
        }
    }
}

echo json_encode($inspections) . "\n";
rsort($inspections);
$result = $inspections[0] * $inspections[1];

echo "Result: $result\n";
