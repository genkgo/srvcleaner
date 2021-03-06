#!/usr/bin/env php
<?php
if (PHP_SAPI !== 'cli') {
    echo 'Warning: Composer should be invoked via the CLI version of PHP, not the '.PHP_SAPI.' SAPI'.PHP_EOL;
}

require __DIR__.'/../vendor/autoload.php';

use Genkgo\Srvcleaner\Console\Application;

error_reporting(-1);

$application = new Application();
$application->run();
