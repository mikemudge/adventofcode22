<?php

include_once __DIR__ . "/../helpers/Grid.php";

$lines = file(dirname(__FILE__) . "/input"); $size = 50;
//$lines = file(dirname(__FILE__) . "/sample"); $size = 4;
global $size;
$part1 = 0;
$part2 = 0;

$lines = array_map("rtrim", $lines);

function move(Grid $grid, int $value, int $facing, mixed $x, mixed $y): array {
    $dx = 0;
    $dy = 0;
    switch ($facing) {
        case 0:
            $dx=1;
            break;
        case 1:
            $dy=1;
            break;
        case 2:
            $dx=-1;
            break;
        case 3:
            $dy=-1;
            break;
    }

    for ($move = 0; $move < $value; $move++) {
        $dis = 0;
        // Wrap through any "whitespace"
        do {
            $dis++;
            $nx = ($x + $grid->getWidth() + $dx * $dis) % $grid->getWidth();
            $ny = ($y + $grid->getHeight() + $dy * $dis) % $grid->getHeight();
            $g = $grid->get($nx, $ny);
        } while ($g == " ");

        // Then check the resulting location for a wall.
        if ($g == "#") {
            // If we hit a wall stop moving.
            return [$x, $y];
        }
        // Update location and repeat
        $x = $nx;
        $y = $ny;
    }
    return [$x, $y];
}

$lastLine = array_pop($lines);
// Remove the empty line separating the last line as well.
array_pop($lines);
$grid = Grid::createFromLines($lines);
$grid->print();
echo($grid->getWidth() . "," . $grid->getHeight() . "\n");

$y = 0;
for ($x = 0; $x < $grid->getWidth(); $x++) {
    if ($grid->get($x, $y) == ".") {
        break;
    }
}

// R,D,L,U = 0,1,2,3
$facings = [">","v",">","^"];
// Start facing right
$facing = 0;
echo("Start at $x, $y, facing $facing\n");

$value = 0;

for ($i = 0; $i < strlen($lastLine); $i++) {
    if ($lastLine[$i] == "R") {
        [$x, $y] = move($grid, $value, $facing, $x, $y);
        // turn right?
        $facing++;
        if ($facing >= 4) {
            $facing -= 4;
        }
        $value = 0;
    } elseif ($lastLine[$i] == "L") {
        [$x, $y] = move($grid, $value, $facing, $x, $y);
        // turn left;
        $facing--;
        if ($facing < 0) {
            $facing += 4;
        }
        $value = 0;
    } else {
         $value = $value * 10 + intval($lastLine[$i]);
    }
}
[$x, $y] = move($grid, $value, $facing, $x, $y);
echo($value . " (no turn)\n");

echo "End at $x, $y, facing $facing\n";

$part1 = ($y + 1) * 1000 + ($x + 1) * 4 + $facing;
echo "Part 1: $part1\n";

echo "\n";
echo "\n";


function move2(Grid $grid, int $value, int $facing, mixed $x, mixed $y): array {
    global $size;

    for ($move = 0; $move < $value; $move++) {
        $dx = 0;
        $dy = 0;
        switch ($facing) {
            case 0:
                $dx=1;
                break;
            case 1:
                $dy=1;
                break;
            case 2:
                $dx=-1;
                break;
            case 3:
                $dy=-1;
                break;
        }


        $nx = ($x + $grid->getWidth() + $dx) % $grid->getWidth();
        $ny = ($y + $grid->getHeight() + $dy) % $grid->getHeight();
        $nfacing = $facing;
        $g = $grid->get($nx, $ny);
        // Wrap when you reach "whitespace"
        if ($g == " ") {
            $cx = floor($nx / $size);
            $cy = floor($ny / $size);

            if ($cy == 0) {
                if ($cx == 0) {
                    if ($facing == 0) {
                        $nfacing = 2;
                        $ny = 2 * $size + ($size - 1 - $ny);
                        $nx = 2 * $size - 1;
                        echo("Moved right off E on to B, $x, $y, $facing -> $nx, $ny, $nfacing\n");
                    } else if ($facing == 1) {
                        $nfacing = 1;
                        $nx = $size * 2 + $nx;
                        $ny = 0;
                        // 19, 199 (down) -> 119, 0 (down)
                        echo("Moved down off N to E, $x, $y, $facing -> $nx, $ny, $nfacing\n");
                    } else if ($facing == 2) {
                        // T -> W
                        $nfacing = 0;
                        $nx = 0;
                        // 49, 4 (left) -> 0, 145 (right)
                        $ny = 2 * $size + ($size - 1 - $ny);
                        echo("Moved left off T on to W, $x, $y, $facing -> $nx, $ny, $nfacing\n");
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            } else if ($cy == 1) {
                if ($cx == 0) {
                    if ($facing == 2) {
                        $nfacing = 1;
                        $nx = ($ny - $size);
                        $ny = 2 * $size;
                        // 50, 56 (left) -> 6, 100 (down
                        echo("Moved left off S on to W, $x, $y, $facing -> $nx, $ny, $nfacing\n");
                    } else if ($facing == 3) {
                        $nfacing = 0;
                        $ny = $size + $nx;
                        $nx = $size;
                        // 18, 100 (up) -> 50, 68 (right)
                        echo("Moved up off W on to S, $x, $y, $facing -> $nx, $ny, $nfacing\n");
                    } else {
                        return false;
                    }
                } else if ($cx == 2) {
                    if ($facing == 0) {
                        $nfacing = 3;
                        $nx = 2 * $size + ($ny - $size);
                        $ny = $size - 1;
                        // 99, 60 (right) -> 110, 49 (up)
                        echo("Moved right off S on to E, $x, $y, $facing -> $nx, $ny, $nfacing\n");
                    } else if ($facing == 1) {
                        $nfacing = 2;
                        $ny = $size + ($nx - 2 * $size);
                        $nx = 2 * $size - 1;
                        // 18, 100 (down) -> 50, 68 (left)
                        echo("Moved down off E on to S, $x, $y, $facing -> $nx, $ny, $nfacing\n");
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            } else if ($cy == 2) {
                if ($cx == 2) {
                    if ($facing == 0) {
                        $nfacing = 2;
                        $nx = $size * 3 - 1;
                        $ny = $size - 1 - ($ny - 2 * $size);
                        // 99, 115 (right) -> 149, 34
                        echo("Moved right off B on to E, $x, $y, $facing -> $nx, $ny, $nfacing\n");
                    } else if ($facing == 2) {
                        $nfacing = 0;
                        $ny = $size - 1 - ($ny - 2 * $size);
                        $nx = 50;
                        // 0, 113 (left) -> 50, 36 (right)
                        echo("Moved left off W on to T, $x, $y, $facing -> $nx, $ny, $nfacing\n");
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            } else if ($cy == 3) {
                if ($cx == 1) {
                    if ($facing == 0) {
                        $nfacing = 3;
                        $nx = $size + ($ny - 3 * $size);
                        $ny = 3 * $size - 1;
                        // 50,156 (right) -> 56, 149 (up)
                        echo("Moved right off N on to B, $x, $y, $facing -> $nx, $ny, $nfacing\n");
                    } else if ($facing == 1) {
                        $nfacing = 2;
                        $ny = 3 * $size + ($nx - $size);
                        $nx = $size - 1;
                        // 79, 149 (down) -> 49, 149 (left)
                        echo("Moved down off B on to N, $x, $y, $facing -> $nx, $ny, $nfacing\n");
                    } else if ($facing == 3) {
                        $nfacing = 0;
                        $ny = 3 * $size + ($nx - $size);
                        $nx = 0;
                        // 62, 0 (up) -> 0, 162 (right)
                        echo("Moved up off T on to N, $x, $y, $facing -> $nx, $ny, $nfacing\n");
                    } else {
                        return false;
                    }
                } else if ($cx == 2) {
                    if ($facing == 2) {
                        $nfacing = 1;
                        $nx = $size + ($ny - 3 * $size);
                        $ny = 0;
                        echo("Moved left off N on to T, $x, $y, $facing -> $nx, $ny, $nfacing\n");
                    } else if ($facing == 3) {
                        $nfacing = 3;
                        $nx = ($nx - $size * 2);
                        $ny = $size * 4 - 1;
                        // 136, 0 (up) -> 36, 199 (up)
                        echo("Moved up off E to N, $x, $y, $facing -> $nx, $ny, $nfacing\n");
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }

        // Then check the resulting location for a wall.
        $g = $grid->get($nx, $ny);
        if ($g == "#") {
            // If we hit a wall stop moving.
//            echo("Hit wall at $nx, $ny, $nfacing\n");
            return [$x, $y, $facing];
        }
        $facings = [">","v","<","^"];
        $grid->set($x, $y, $facings[$facing]);
        // Update location and repeat
        $x = $nx;
        $y = $ny;
        $facing = $nfacing;
    }
    return [$x, $y, $facing];
}

function solvePart2(Grid $grid, $lastLine): int {
    $y = 0;
    for ($x = 0; $x < $grid->getWidth(); $x++) {
        if ($grid->get($x, $y) == ".") {
            break;
        }
    }

// Start facing right
    $facing = 0;
    echo("Start at $x, $y, facing $facing\n");

    $value = 0;

    for ($i = 0; $i < strlen($lastLine); $i++) {
        if ($lastLine[$i] == "R") {
            [$x, $y, $facing] = move2($grid, $value, $facing, $x, $y);
            // turn right?
            $facing++;
            if ($facing >= 4) {
                $facing -= 4;
            }
            $value = 0;
        } elseif ($lastLine[$i] == "L") {
            [$x, $y, $facing] = move2($grid, $value, $facing, $x, $y);
            // turn left;
            $facing--;
            if ($facing < 0) {
                $facing += 4;
            }
            $value = 0;
        } else {
            $value = $value * 10 + intval($lastLine[$i]);
        }
    }
    [$x, $y, $facing] = move2($grid, $value, $facing, $x, $y);
    echo($value . " (no turn)\n");

    $grid->print();

    echo "End at $x, $y, facing $facing\n";

    return ($y + 1) * 1000 + ($x + 1) * 4 + $facing;
}

$part2 = solvePart2($grid, $lastLine);

// 144252 is too high
// 25493 is too low
echo "Part 2: $part2\n";
