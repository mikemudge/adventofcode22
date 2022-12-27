<?php

$lines = file(dirname(__FILE__) . "/input");
//$lines = file(dirname(__FILE__) . "/sample");
$part1 = 0;
$part2 = 0;

$lines = array_map("trim", $lines);

class Node {
    public Node $next;
    public Node $prev;
    public int $val;
    public function __construct(int $val) {
        $this->val = $val;
    }
}

function solve($lines, $decryptKey, $mixCount) {
    // $nums is the original ordering
    $nums = [];
    foreach ($lines as $line) {
        $nums[] = new Node(intval($line) * $decryptKey);
    }

    $zero = null;
    //echo(json_encode($nums) . PHP_EOL);
    $numNodes = count($nums);
    for ($i = 0; $i < $numNodes; $i++) {
        $n = $nums[$i];
        if ($n->val === 0) {
            $zero = $n;
        }
        if ($i == $numNodes - 1) {
            $next = $nums[0];
        } else {
            $next = $nums[$i + 1];
        }
        $nums[$i]->next = $next;
        $next->prev = $nums[$i];
    }

    for ($mix = 0; $mix < $mixCount; $mix++) {
        foreach ($nums as $num) {
            // Using -1 for nodes, as the current number doesn't move past itself.
            $move = $num->val % ($numNodes - 1);
            //    echo("Moving $move\n");
            if ($move == 0) {
                // No move happens.
                continue;
            }
            // Remove this node from the link.
            $num->prev->next = $num->next;
            $num->next->prev = $num->prev;

            $n = $num->prev;
            if ($move < 0) {
                // Move backwards.
                for ($i = 0; $i < -$move; $i++) {
                    $n = $n->prev;
                }
            } else {
                // Move forwards.
                for ($i = 0; $i < $move; $i++) {
                    $n = $n->next;
                }
            }
            // Put num between n and n->next;
            $num->next = $n->next;
            $num->prev = $n;
            // Then also update those nodes to point to num
            $num->next->prev = $num;
            $num->prev->next = $num;

            // Print out the 3 around the new spot.
            //    echo($num->prev->val . " -> " . $num->val . " -> " . $num->next->val . PHP_EOL);
        }
    }

    $sum = 0;
    $n = $zero;
    for ($i = 0; $i < $numNodes; $i++) {
        if (1000 % $numNodes === $i) {
            echo($n->val . " at 1000\n");
            $sum += $n->val;
        }
        if (2000 % $numNodes === $i) {
            echo($n->val . " at 2000\n");
            $sum += $n->val;
        }
        if (3000 % $numNodes === $i) {
            echo($n->val . " at 3000\n");
            $sum += $n->val;
        }
        $n = $n->next;
    }
    return $sum;
}

$part1 = solve($lines, 1, 1);
$part2 = solve($lines, 811589153, 10);

echo "Part 1: $part1\n";
echo "Part 2: $part2\n";
