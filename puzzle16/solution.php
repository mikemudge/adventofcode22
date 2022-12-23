<?php

$lines = file(dirname(__FILE__) . "/input");
//$lines = file(dirname(__FILE__) . "/sample");
$part1 = 0;
$part2 = 0;

$lines = array_map("trim", $lines);

$nodes = [];
for($i = 0; $i < count($lines); $i++) {
    $line = $lines[$i];
//    echo("$line\n");
    [$left, $right] = explode("; ", $line);
    [$name,$rate] = explode(" has flow rate=", substr($left, 6));
    $rate = intval($rate);
    if (str_starts_with($right, "tunnels lead to valves ")) {
        $valves = explode(", ", substr($right, 23));
    } else {
        $valves = explode(", ", substr($right, 22));
    }
    echo("Valve $name with rate=$rate connects to " . json_encode($valves) . PHP_EOL);
    // Build a graph and find a path.
    $nodes[$name] = [
        "tunnels" => $valves,
        "rate" => $rate
    ];
}

// Calculate the maximum amount of pressure which can be released.
$pressureRate = 0;
foreach($nodes as $node) {
    $pressureRate = $node['rate'];
}

// Translate into a new graph.
// Use weighted edges to represent travel time between nodes.
$nodes2 = [];
function shortestPaths(array $nodes, string $start) {
    $next = [$start];
    $distances = [];
    $distances[$start] = 0;

    while(!empty($next)) {
        $tmp = $next;
        $next = [];
        foreach ($tmp as $name) {
            $tunnels = $nodes[$name]['tunnels'];
            foreach ($tunnels as $tunnel) {
                // If we don't already have a way to reach this, add one.
                if (!array_key_exists($tunnel, $distances)) {
                    $distances[$tunnel] = 1 + $distances[$name];
                    // A new node was discovered, so use it next time.
                    $next[] = $tunnel;
                }
            }
        }
    }

    // Remove the path to yourself as its not helpful.
    unset($distances[$start]);
    return $distances;

}

foreach($nodes as $name => $node) {
    // Only include nodes which have a value in enabling.
    if ($name == "AA" || $node['rate'] > 0) {
        echo("transforming $name\n");

        $paths = shortestPaths($nodes, $name);
        foreach($paths as $p=>$cost) {
            // Don't consider paths to nodes with 0 rate.
            if ($nodes[$p]['rate'] == 0) {
                unset($paths[$p]);
            }
        }

        $nodes2[$name] = [
            'rate' => $node['rate'],
            'paths' => $paths
        ];

    }
}

foreach ($nodes2 as $name=>$node) {
    echo($name . " -> " . json_encode($nodes2[$name]) . PHP_EOL);
}

function solvePart1($nodes, $nodes2) {
    // Initially at AA with 30 minutes, no pressure released and no nodes enabled.
    $state = ["AA", 30, 0, []];
    $pq = new SplPriorityQueue();
    $pq->insert($state, 0);
    $bestPressure = 0;
    $bestVisited = [];
    $ranOutOfTime = 0;
    $queueReads = 0;
    while($pq->valid()) {
        $queueReads++;
        $state = $pq->extract();
        [$name, $timeRemaining, $pressure, $visited] = $state;
        if ($pressure > $bestPressure) {
            $bestVisited = $visited;
        }
        $bestPressure = max($pressure, $bestPressure);
        foreach ($nodes2[$name]['paths'] as $p=>$cost) {
            if (array_key_exists($p, $visited)) {
                // Can't reenable this node.
                continue;
            }
            $rate = $nodes[$p]['rate'];
            if ($rate == 0) {
                // No point in activating this node.
                continue;
            }
            // travel time + open valve time.
            $dt = $cost + 1;
            if ($dt > $timeRemaining) {
                $ranOutOfTime++;
                // Its not possible to make it and enable in the time remaining.
                continue;
            }
            // Turning on the value at time $time + $dt will release pressure.
            // Calculate how much pressure it releases given the remaining time.
            $dp = ($timeRemaining - $dt) * $rate;
            $visit2 = $visited;
            $visit2[$p] = $timeRemaining - $dt;
            $priority = 0;
            $priority = 1000000 - ($pressure + $dp + hueristic1($timeRemaining - $dt, $visit2, $nodes));
            $pq->insert([$p, $timeRemaining - $dt, $pressure + $dp, $visit2], $priority);
        }
    }

    echo("queueIterations " . $queueReads . PHP_EOL);
    echo("out of time " . $ranOutOfTime . PHP_EOL);
    echo(json_encode($bestVisited) . PHP_EOL);
    return $bestPressure;
}

function solvePart2($nodes, $nodes2) {
    // Initially both at AA with 26 minutes, no pressure released and no nodes enabled.
    $remaining = [];
    foreach ($nodes as $name=>$n) {
        if ($n['rate'] == 0) {
            // No point in going to this node.
            continue;
        }
        $remaining[$name] = true;
    }
    $state = ["AA", "AA", 26, 26, 0, $remaining];
    $pq = new SplPriorityQueue();
    $pq->setExtractFlags(SplPriorityQueue::EXTR_BOTH);
    $pq->insert($state, 0);
    $queueReads = 0;
    $bestPressure = 0;
    while($pq->valid()) {
        $queueReads++;
        $item = $pq->extract();
        $state = $item['data'];
        [$name, $eleName, $timeRemaining, $eleTimeRemaining, $pressure, $toVisit] = $state;
        if ($queueReads % 100000 == 0) {
            echo($queueReads . " " . (1000000 - $item['priority']) . PHP_EOL);
            echo(json_encode($state) . PHP_EOL);
        }
        $bestPressure = max($pressure, $bestPressure);
        if (empty($toVisit)) {
            // Winner
            echo("Winner $pressure" . PHP_EOL);
            break;
        }
        foreach ($toVisit as $next=>$x) {
            // Decide who can move to next.
            $rate = $nodes[$next]['rate'];
            $costMe = $nodes2[$name]['paths'][$next] + 1;
            $costEle = $nodes2[$eleName]['paths'][$next] + 1;

            if ($eleTimeRemaining - $costEle >= 0 && $timeRemaining - $costMe < $eleTimeRemaining - $costEle) {
                // Ele moves.
                // Turning on the value at time $time + $dt will release pressure.
                // Calculate how much pressure it releases given the remaining time.
                $dp = ($eleTimeRemaining - $costEle) * $rate;
                $visit2 = $toVisit;
                unset($visit2[$next]);
//                $priority = 0;
                $priority = 1000000 - ($pressure + $dp + hueristic($timeRemaining, $eleTimeRemaining - $costEle, $visit2, $nodes));
                $pq->insert([$name, $next, $timeRemaining, $eleTimeRemaining - $costEle, $pressure + $dp, $visit2], $priority);
            } else if ($timeRemaining - $costMe >= 0) {
                // I move.
                // Turning on the value at time $time + $dt will release pressure.
                // Calculate how much pressure it releases given the remaining time.
                $dp = ($timeRemaining - $costMe) * $rate;
                $visit2 = $toVisit;
                unset($visit2[$next]);
//                $priority = 0;
                $priority = 1000000 - ($pressure + $dp + hueristic($timeRemaining - $costMe, $eleTimeRemaining, $visit2, $nodes));
                $pq->insert([$next, $eleName, $timeRemaining - $costMe, $eleTimeRemaining, $pressure + $dp, $visit2], $priority);
            }
        }
    }

    echo("queueIterations " . $queueReads . PHP_EOL);

    return $bestPressure;
}

function hueristic1(int $timeRemaining, array $visit2, array $nodes): int {
    $sum = 0;
    foreach($nodes as $name=>$node) {
        if (array_key_exists($name, $visit2)) {
            continue;
        }
        $sum += $node['rate'];
    }
    return $sum * $timeRemaining;
}
function hueristic(int $timeRemaining, int $eleTimeRemaining, array $visit2, array $nodes) {
    $mostTime = max($timeRemaining, $eleTimeRemaining);
    $sum = 0;
    foreach($visit2 as $name=>$node) {
        $sum += $nodes[$name]['rate'];
    }
    return $sum * $mostTime;
}

$part1 = solvePart1($nodes, $nodes2);
echo "Part 1: $part1\n";

$part2 = solvePart2($nodes, $nodes2);
echo "Part 2: $part2\n";
