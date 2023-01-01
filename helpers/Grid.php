<?php

class Grid {
    private int $width;
    private int $height;
    private array $rows;

    public function __construct($width, $height, $value) {
        $this->width = $width;
        $this->height = $height;
        $this->rows = [];
        for($y = 0; $y < $height; $y++) {
            $this->rows[$y] = [];
            for($x = 0; $x < $width; $x++) {
                $this->rows[$y][$x] = $value;
            }
        }
    }

    public static function createFromLines($lines): Grid {
        $height = count($lines);
        $width = 0;
        foreach($lines as $line) {
            $width = max($width, strlen($line));
        }
        $grid = new Grid($width, $height, " ");
        foreach($lines as $y=>$line) {
            $row = str_split($line);
            foreach ($row as $x => $v) {
                $grid->set($x, $y, $v);
            }
        }
        return $grid;
    }
    public function print($x = 0, $top = 0, $width = null, $height = null): void {
        if ($width === null) {
            $width = $this->width;
        }
        if ($height === null) {
            $height = $this->height;
        }
        for ($y = $top; $y < $top + $height; $y++) {
            $row = $this->rows[$y];
            echo(join("", array_slice($row, $x, $width)) . "\n");
        }
    }

    public function find(string $string) {
        for($y = 0; $y < count($this->rows); $y++) {
            for($x = 0; $x < count($this->rows[$y]); $x++) {
                if ($this->rows[$y][$x] == $string) {
                    return [$x, $y];
                }
            }
        }
    }

    public function set(int $x, int $y, mixed $value) {
        if ($x < 0 || $y < 0) {
            throw new Exception("Out of y-bounds of grid $x,$y");
        }
        if ($y >= $this->height || $x >= $this->width) {
            throw new Exception("Out of x-bounds of grid $x,$y");
        }
        $this->rows[$y][$x] = $value;
    }

    public function get(int $x, int $y) {
        if ($x < 0 || $y < 0) {
            return null;
        }
        if ($y >= $this->height || $x >= $this->width) {
            return null;
        }
        return $this->rows[$y][$x];
    }

    public function getHeight(): int {
        return $this->height;
    }

    public function getWidth(): int {
        return $this->width;
    }

    public function getSubGrid(int $offx, int $offy, int $w, int $h): Grid {
        $g = new Grid($w, $h, '');
        for($y = 0; $y < $h; $y++) {
            for($x = 0; $x < $w; $x++) {
                $g->set($x, $y, $this->rows[$offy + $y][$offx + $x]);
            }
        }
        return $g;
    }
}