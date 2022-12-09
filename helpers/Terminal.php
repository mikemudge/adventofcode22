<?php

class Terminal {
    // The root folder.
    private FileDirectory $root;
    // The path to the current folder
    private array $currentPath = [];
    // The current directory
    private FileDirectory $currentDir;

    public function __construct() {
        $this->root = new FileDirectory(null, "/");
    }

    public function evaluate(array $lines) {
        foreach ($lines as $line) {
            $parts = explode(" ", $line);
            if ($parts[0] == "$") {
                // This is a command.
                $lastCommand = $parts;
                if ($parts[1] === "cd") {
                    $this->changeDir($parts[2]);
                } elseif ($parts[1] == "ls") {
                    // Indicates that some following lines will contain output?
                    continue;
                } else {
                    throw new Exception("Unknown command $line");
                }
            } else {
                // Output from previous command?
                if ($parts[0] == "dir") {
                    $this->currentDir->addDir($parts[1]);
                } else {
                    $size = intval($parts[0]);
                    $this->currentDir->addFile($parts[1], $size);
                }
            }
        }
    }

    private function changeDir(string $path) {
        if ($path == "/") {
            $this->currentPath = [];
            $this->currentDir = $this->root;
        } else if ($path == "..") {
            array_pop($this->currentPath);
            $this->currentDir = $this->currentDir->getParent();
        } else {
            $this->currentPath[] = $path;
            $this->currentDir = $this->currentDir->getSubDir($path);
        }
    }

    public function getRoot(): FileDirectory {
        return $this->root;
    }
}

class FileDirectory {
    private FileDirectory|null $parent;
    private array $files;
    private array $subdirs;
    private int|null $size;

    public function __construct($parent, string $name) {
        $this->parent = $parent;
        $this->name = $name;
        $this->size = null;
        $this->files = [];
        $this->subdirs = [];
    }

    public function getParent(): FileDirectory {
        return $this->parent;
    }

    public function addDir(string $dirname): void {
        // Add a new empty dir.
        $this->subdirs[$dirname] = new FileDirectory($this, $dirname);
    }

    public function addFile(string $filename, int $size): void {
        $this->files[] = [
            'name' => $filename,
            'size' => $size
        ];
    }

    public function getSubDir(string $path): FileDirectory {
        return $this->subdirs[$path];
    }

    public function getAllSubDirs(): array {
        $all = [$this];
        foreach($this->subdirs as $dir) {
            $all = array_merge($all, $dir->getAllSubDirs());
        }
        return $all;
    }
    public function getSize(): int {
        if ($this->size !== null) {
            return $this->size;
        }
        $size = 0;
        foreach ($this->files as $file) {
            $size += $file['size'];
        }
        foreach($this->subdirs as $dir) {
            $size += $dir->getSize();
        }
        $this->size = $size;
        return $this->size;
    }

    public function print(string $indent, int $level): void {
        // Print the folder name
        echo str_repeat($indent, $level) . "- $this->name (dir, size=" . $this->getSize() . ")\n";
        // Then the files
        foreach ($this->files as $file) {
            echo str_repeat($indent, $level + 1) . "- " . $file['name'] . " (file, size=". $file['size'] . ")\n";
        }
        // Then sub directories.
        foreach($this->subdirs as $dir) {
            $dir->print($indent, $level + 1);
        }
    }
}
