<?php
require_once __DIR__ . '/vendor/autoload.php';

use Symfony\Component\Console\{
    Command\Command,
    Input\InputInterface,
    Output\OutputInterface,
    Style\SymfonyStyle
};
use React\EventLoop\Loop;
use React\Stream\ReadableResourceStream;

class LogMonitorCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('log:monitor');
        $this->setDescription('Monitors log entries from the console.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $loop = Loop::get();

        $stats = [];

        $updateStats = function () use (&$stats, $io, $output) {
            // Clear the entire screen
            $output->write("\033[2J");
            // Move cursor to the top of the screen
            $output->write("\033[H");
            // Print the static header information
            $io->title('Log Monitor');
            
            $io->writeln('Type log messages below:');
            // Print dynamic stats under the static text
            foreach ($stats as $level => $count) {
                $io->section("$level: $count");
            }
            $io->write('> '); // Prompt
        };

        $loop->addPeriodicTimer(1, $updateStats);

        $stdin = new ReadableResourceStream(STDIN, $loop);
        $stdin->on('data', function ($chunk) use (&$stats, $io) {
            $lines = explode("\n", trim($chunk));
            foreach ($lines as $line) {
                if (str_contains($line, '.DEBUG:')) continue;
                if (preg_match('/test\.(\w+):/', $line, $matches)) {
                    $level = strtolower($matches[1]);
                    $stats[$level] = ($stats[$level] ?? 0) + 1;
                }
            }
        });

        $loop->run();

        return Command::SUCCESS;
    }
}

$application = new \Symfony\Component\Console\Application();
$application->add(new LogMonitorCommand());
$application->run();
