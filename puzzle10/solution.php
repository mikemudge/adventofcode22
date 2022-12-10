<?php

include_once __DIR__ . "/../helpers/Computer.php";

$lines = file(dirname(__FILE__) . "/input");
//$lines = file(dirname(__FILE__) . "/sample");

$lines = array_map("trim", $lines);

$part1 = 0;
$part2 = 0;

$instructions = $lines;
$computer = new Computer($instructions);
$i = 0;
$pixels = [];
while($computer->isRunning()) {
    $i++;
    $computer->executeToCycle($i);
    $x = $computer->getX();
    $pixels[] = abs($x - (($i - 1) % 40)) < 2 ? "#" : ".";
    if ($i % 40 == 20) {
        $strength = $i * $x;
        echo("$i x=$x strength=$strength\n");
        $part1 += $strength;
    }
}

$display = join($pixels);
for ($i = 0; $i < 6; $i++) {
    echo(substr($display, 40 * $i, 40). "\n");
}
echo "Part 1: $part1\n";

// This was read from the display printed above.
###..#....####.####.####.#.....##..####.
#..#.#....#.......#.#....#....#..#.#....
#..#.#....###....#..###..#....#....###..
###..#....#.....#...#....#....#.##.#....
#.#..#....#....#....#....#....#..#.#....
#..#.####.####.####.#....####..###.####.
$part2 = "RLEZFLGE";
echo "Part 2: $part2\n";
