<?php
declare(strict_types=1);

// Anonymous function as a filter
$filter = function(string $line): bool {
    // filtering out .DEBUG keyword
    // useful when excluding things
    return !str_contains($line, '.DEBUG:');
};

// Anonymous function as a decorator
$decorator = function(string $line): ?string {
    // useful for categorization and analyzing the log messages
    if (preg_match('/test\.(\w+):/', $line, $matches)) {
        return strtolower($matches[1]);
    }
    return null;
};

// Generator function to read the file line by line
function readFileLineByLine(string $filename): iterable {
    // opening file
    $handle = fopen($filename, "r");
    if ($handle) {
        while (($line = fgets($handle)) !== false) {
            // paused and values are beeing sent back to the caller
            // current position in the file is beein preserved
            // key feature of the generator
            // only one line saved in the memory
            yield $line;
        }
        //closing file
        fclose($handle);
    }
}

// Main function to process the log file and build statistics
function processLogFile(string $filename, callable $filter, callable $decorator): array {
    $stats = [];
    foreach (readFileLineByLine($filename) as $line) {
        if ($filter($line)) {
            $level = $decorator($line);
            if ($level !== null) {
                $stats[$level] = ($stats[$level] ?? 0) + 1;
            }
        }
    }
    arsort($stats);
    return $stats;
}




// CLI entry point// CLI entry point
function main(array $argv): void {
    if (count($argv) < 2) {
        // usage if used wrong
        echo "Usage: php " . $argv[0] . " filename.log" . PHP_EOL;
        exit(1);
    }
    $filename = $argv[1];
    $stats = processLogFile($filename, $GLOBALS['filter'], $GLOBALS['decorator']);
    foreach ($stats as $level => $count) {
        echo "$level: $count" . PHP_EOL;
    }
}

main($argv);



