<?php

require __DIR__.'/../vendor/autoload.php';

// script needs to handle huge files (e.g. bluray rips with 16GB)
ini_set('memory_limit', -1);

use Symfony\Component\Console\Application;

$application = new \Mihaeu\SubCollectorApplication();
//$application->add(new Mihaeu\DownloadCommand());
$application->run();
