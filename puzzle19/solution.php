<?php

$lines = file(dirname(__FILE__) . "/input");
//$lines = file(dirname(__FILE__) . "/sample");
$part1 = 0;
$part2 = 0;

$lines = array_map("trim", $lines);

$blueprints = [];
foreach ($lines as $line) {
    [$bp, $recipes] = explode(": ", substr($line, 0, -1));
    $robots = explode(". ", $recipes);
    $blueprint = [];
    foreach($robots as $robot) {
        $parts = explode(" ", $robot);
        $type = $parts[1];
        $costs = [];
        for ($i=4;$i<count($parts); $i+=3) {
            $costs[$parts[$i + 1]] = intval($parts[$i]);
        }
        $blueprint[$type] = $costs;
    }
    $blueprints[] = $blueprint;
}

echo(json_encode($blueprints,  JSON_PRETTY_PRINT) . "\n");


function makeRobots($materials, $robots, string $makeNext, int $time, $maxTime, $stopClay, $blueprint): int {
    $robotCost = $blueprint[$makeNext];
    for ($i = $time; $i < $maxTime; $i++) {
        $canAfford = true;
        foreach ($robotCost as $m => $amount) {
            if ($materials[$m] < $amount) {
                $canAfford = false;
            }
        }
        // Always gather resources.
        $materials['ore'] += $robots['ore'];
        $materials['clay'] += $robots['clay'];
        $materials['obsidian'] += $robots['obsidian'];
        $materials['geode'] += $robots['geode'];

        // If the robot was affordable, it finishes being made now.
        if ($canAfford) {
            // Deduct costs
            $robotCost = $blueprint[$makeNext];
            foreach ($robotCost as $m => $amount) {
                $materials[$m] -= $amount;
            }
            $robots[$makeNext]++;

            // Now decide what robot type to make next.
            $bestGeodes = 0;

            // Determine the max ore cost for any robot, there is no point having more ore robots than this.
            // We can only create 1 robot per cycle, so higher income is not spendable.
            $oreCost = 0;
            $clayCost = 0;
            foreach($blueprint as $cost) {
                $oreCost = max($oreCost, $cost['ore']);
                if (isset($cost['clay'])) {
                    $clayCost = max($clayCost, $cost['clay']);
                }
            }
            // After this time you must have one obsidian robot per day, otherwise no geodes are possible.
            if ($time <= $stopClay) {
                if ($robots['ore'] < $oreCost) {
                    // We could still use more ore robots.
                    $bestGeodes = max($bestGeodes, makeRobots($materials, $robots, "ore", $i + 1, $maxTime, $stopClay, $blueprint));
                }

                if ($robots['clay'] < $clayCost) {
                    $bestGeodes = max($bestGeodes, makeRobots($materials, $robots, "clay", $i + 1, $maxTime, $stopClay, $blueprint));
                }
            }
            if ($robots['clay'] > 0) {
                // We could make an obsidian one if we have clay coming in.
                $bestGeodes = max($bestGeodes, makeRobots($materials, $robots, "obsidian", $i + 1, $maxTime, $stopClay, $blueprint));
            }
            if ($robots['obsidian'] > 0) {
                // We could make a geode one if we have obsidian coming in.
                $bestGeodes = max($bestGeodes, makeRobots($materials, $robots, "geode", $i + 1, $maxTime, $stopClay, $blueprint));
            }
            return $bestGeodes;
        }
    }
    // When we run out of time, we just return the number of geodes we have.
    return $materials['geode'];
}

function getMostGeodes(array $blueprint, int $time) {
    $robots = [
        'ore' => 1,
        'clay' => 0,
        'obsidian' => 0,
        'geode' => 0
    ];
    $materials = [
        'ore' => 0,
        'clay' => 0,
        'obsidian' => 0,
        'geode' => 0
    ];

    // Calculate a time to stop making clay robots based on geode/obsidian costs.
    // E.g a geode robot which costs 15 obsidian requires minimum 6 minutes to create.
    // Minute 1-5 create an obsidian mining robot.
    // Mine 0,1,2,3,4,5 obsidian each minute for a total of 15.

    $minutesGeode = getMinutes($blueprint['geode']['obsidian']);
//    echo "Cost of obsidian for a geode robot " . $blueprint['geode']['obsidian'] . " " . $minutesGeode . PHP_EOL;
//    echo "Cost of clay for an obsidian robot " . $blueprint['obsidian']['clay'] . " " . $minutesObsidian . PHP_EOL;

    // At this point we must make only obsidian robots to have a geode robot mine anything before the end.
    $stopClay = $time - 2 - $minutesGeode;
    // Either make an ore robot or a clay robot first.
    $geodes = makeRobots($materials, $robots, 'ore', 0, $time, $stopClay, $blueprint);
    return max($geodes, makeRobots($materials, $robots, 'clay', 0, $time, $stopClay, $blueprint));
}

function getMinutes(int $x): int {
    // 1, 2, 3, 4, 5, 6
    // 0, 1, 3, 6, 10, 15

//    1 -> 2
//    2-3 -> 3
//    4-6 -> 4
//    7-10 -> 5
//    11-15 -> 6
    if ($x > 10) {
        return 6;
    }
    if ($x > 6) {
        return 5;
    }
    if ($x > 3) {
        return 4;
    }
    if ($x > 1) {
        return 3;
    }
    if ($x > 0) {
        return 2;
    }
    throw new RuntimeException("Impossible cost $x");
}

$part2 = 1;
foreach ($blueprints as $i => $blueprint) {
    $geodes = getMostGeodes($blueprint, 24);
    echo("Blueprint $i part1: $geodes\n");
    $part1 += ($i + 1) * $geodes;
    if ($i < 3) {
        $geodes = getMostGeodes($blueprint, 32);
        echo("Blueprint $i part2: $geodes\n");
        $part2 *= $geodes;
    }
}
echo "Part 1: $part1\n";
// 12168 was too low.
echo "Part 2: $part2\n";

// Blueprint 0 part2: 26
// Blueprint 1 part2: 52
// Blueprint 2 part2: 9