<?php

$lines = file(dirname(__FILE__) . "/input");
//$lines = file(dirname(__FILE__) . "/sample");
$part1 = 0;
$part2 = 0;

$lines = array_map("trim", $lines);

foreach($lines as $line) {
    [$name, $value] = explode(": ", $line);
    $nodes[$name] = $value;
}

function calc($value, $nodes) {
    $parts = explode(" ", $value);
    if (count($parts) == 1) {
        return intval($parts[0]);
    } else {
        if ($parts[0] == "humn") {
            echo("Found humn\n");
        }
        if ($parts[2] == "humn") {
            echo("Found humn\n");
        }
        $lhs = calc($nodes[$parts[0]], $nodes);
        $rhs = calc($nodes[$parts[2]], $nodes);
        $op = $parts[1];
        switch($op) {
            case "-":
                return $lhs - $rhs;
            case "+":
                return $lhs + $rhs;
            case "*":
                return $lhs * $rhs;
            case "/":
                return $lhs / $rhs;
            default:
                throw new RuntimeException("Unknown op $op");
        }
    }
}

function containsHumn(string $key, $nodes) {
    if ($key == "humn") {
        return true;
    }
    $value = $nodes[$key];
    $parts = explode(" ", $value);
    if (count($parts) == 1) {
        return false;
    } else {
        [$l, $op, $r] = $parts;
        return containsHumn($l, $nodes) || containsHumn($r, $nodes);
    }
}

function makeEqual($num, string $key, $nodes) {
    echo("$key = $num\n");
    if ($key == "humn") {
        return $num;
    }
    $value = $nodes[$key];
    $parts = explode(" ", $value);
    if (count($parts) == 1) {
        throw new RuntimeException("Unexpected leaf for $key");
    } else {
        [$l, $op, $r] = $parts;
        $rightContains = containsHumn($r, $nodes);
        if ($rightContains) {
            $lhs = calc($nodes[$l], $nodes);
            echo("$lhs $op $r = $num\n");
            // a + x = b -> x = b - a;
            // a - x = b -> x = a - b;
            // a * x = b -> x = b / a;
            // a / x = b -> x = a / b;
            switch($op) {
                case "+":
                    return makeEqual($num - $lhs, $r, $nodes);
                case "-":
                    return makeEqual($lhs - $num, $r, $nodes);
                case "*":
                    return makeEqual($num / $lhs, $r, $nodes);
                case "/":
                    return makeEqual($lhs / $num, $r, $nodes);
            }
        } else {
            // Assume rhs is an int, because lhs wasn't
            $rhs = calc($nodes[$r], $nodes);
            echo("$l $op $rhs = $num\n");
            // x + a = b -> x = b - a;
            // x - a = b -> x = b + a;
            // x * a = b -> x = b / a;
            // x / a = b -> x = b * a;
            switch($op) {
                case "+":
                    return makeEqual($num - $rhs, $l, $nodes);
                case "-":
                    return makeEqual($num + $rhs, $l, $nodes);
                case "*":
                    return makeEqual($num / $rhs, $l, $nodes);
                case "/":
                    return makeEqual($num * $rhs, $l, $nodes);
            }
        }
        return -1;
    }
}

$part1 = calc($nodes['root'], $nodes);

echo "Part 1: $part1\n";


// Figure out what value humn: should have to get left and right side equal from root.
$parts = explode(" ", $nodes['root']);

$rhs = calc($nodes[$parts[2]], $nodes);
echo("right " . $parts[2] . " $rhs\n");

// We know $rhs is an int, and nodes of the lhs must equal it.
$part2 = makeEqual($rhs, $parts[0], $nodes);
echo "Part 2: $part2\n";
