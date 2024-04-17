<?php
declare(strict_types=1);

// exclude debug messages
$filter = function(string $line): bool {
    return !str_contains($line, '.DEBUG:');
};

// extract the log level
$decorator = function(string $line): ?string {
    if (preg_match('/test\.(\w+):/', $line, $matches)) {
        return strtolower($matches[1]);
    }
    return null;
};

// read data from input stream
function readStream(): iterable {
    $handle = fopen("php://stdin", "r");
    if ($handle) {
        while (($line = fgets($handle)) !== false) {
            yield $line;
        }
        fclose($handle);
    }
}

// process log data and build statistics
function processLogStream(callable $filter, callable $decorator): void {
    $stats = [];
    $updateInterval = 10; // Update interval set to 10 seconds
    $nextUpdate = time() + $updateInterval;

    foreach (readStream() as $line) {
        if ($filter($line)) {
            $level = $decorator($line);
            if ($level !== null) {
                $stats[$level] = ($stats[$level] ?? 0) + 1;
            }
        }

        if (time() >= $nextUpdate) {
            arsort($stats);
            foreach ($stats as $level => $count) {
                echo "$level: $count" . PHP_EOL;
            }
            $nextUpdate = time() + $updateInterval;
        }
    }
}

// Signal handling for clean exit
pcntl_async_signals(true);
pcntl_signal(SIGINT, function() {
    echo "Exiting..." . PHP_EOL;
    exit;
});

// CLI entry point
processLogStream($GLOBALS['filter'], $GLOBALS['decorator']);
