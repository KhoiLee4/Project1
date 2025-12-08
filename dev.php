<?php

$hasPcntl = function_exists('pcntl_fork');

$commands = [
    'php artisan serve',
    'php artisan queue:listen --tries=1',
];

$names = ['server', 'queue'];
$colors = ['#93c5fd', '#c4b5fd'];

if ($hasPcntl) {
    $commands[] = 'php artisan pail --timeout=0';
    $names[] = 'logs';
    $colors[] = '#fb7185';
}

$commands[] = 'npm run dev';
$names[] = 'vite';
$colors[] = '#fdba74';

$colorsStr = implode(',', $colors);
$namesStr = implode(',', $names);
$commandsStr = implode(' ', array_map(fn($cmd) => '"' . $cmd . '"', $commands));

$fullCommand = sprintf(
    'npx concurrently -c "%s" %s --names=%s --kill-others',
    $colorsStr,
    $commandsStr,
    $namesStr
);

passthru($fullCommand);

