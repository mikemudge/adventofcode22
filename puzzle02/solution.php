<?php

$lines = file(dirname(__FILE__) . "/input");

$part1 = 0;
$part2 = 0;
foreach($lines as $line) {
    $line = trim($line);

    [$a, $b] = explode(" ", $line);

    $part1 += getScore($a, $b);
    $part2 += getScore2($a, $b);
}

echo "Part 1: $part1\n";
echo "Part 2: $part2\n";

function getScore($a, $b) {

    switch($a) {
        case "A": // they play Rock
            switch($b) {
                case "X": // you play Rock
                    return 1 + 3;
                case "Y": // you play Paper
                    return 2 + 6;
                case "Z": // you play Scissors
                    return 3;
            }
        case "B": // they play Paper
            switch($b) {
                case "X": // you play Rock
                    return 1;
                case "Y": // you play Paper
                    return 2 + 3;
                case "Z": // you play Scissors
                    return 3 + 6;
            }
        case "C": // They play Scissors
            switch($b) {
                case "X": // you play Rock
                    return 1 + 6;
                case "Y": // you play Paper
                    return 2;
                case "Z": // you play Scissors
                    return 3 + 3;
            }
    }
}

function getScore2($a, $b) {

//    Rock = 1
//    Paper = 2
//    Scissors = 3
    switch ($a) {
        case "A": // they play Rock
            switch ($b) {
                case "X": // you lose
                    return 3;
                case "Y": // you draw
                    return 1 + 3;
                case "Z": // you win
                    return 2 + 6;
            }
        case "B": // they play Paper
            switch ($b) {
                case "X": // you lose
                    return 1;
                case "Y": // you draw
                    return 2 + 3;
                case "Z": // you win
                    return 3 + 6;
            }
        case "C": // They play Scissors
            switch ($b) {
                case "X": // you lose
                    return 2;
                case "Y": // you draw
                    return 3 + 3;
                case "Z": // you win
                    return 1 + 6;
            }
    }
}