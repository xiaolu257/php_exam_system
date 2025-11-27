<?php
declare(strict_types=1);

return [
    'driver' => Hyperf\Watcher\Driver\ScanFileDriver::class,
    'bin' => 'php',
    'command' => 'bin/hyperf.php start',
    'watch' => [
        'dir' => ['app', 'config', 'test'],
        'file' => ['.env'],
        'scan_interval' => 2000,
    ],
];