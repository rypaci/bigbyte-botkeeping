<?php
$path = __DIR__ . '/storage/logs/laravel.log';
$c = file_get_contents($path);
$lines = explode("\n", $c);
echo count($lines) . " total lines\n";
$tail = array_slice($lines, -8);
foreach ($tail as $l) {
    echo $l . "\n";
}
