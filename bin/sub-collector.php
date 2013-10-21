<?php

require __DIR__.'/../vendor/autoload.php';

use \Mihaeu\Console\SubCollectorApplication;
use \Mihaeu\Console\DownloadCommand;

// script needs to handle huge files (e.g. bluray rips with 16GB)
ini_set('memory_limit', -1);

use Symfony\Component\Console\Application;

$application = new SubCollectorApplication();
$application->add(new DownloadCommand());
$application->run();
