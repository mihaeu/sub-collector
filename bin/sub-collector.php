#!/usr/bin/env php
<?php

$files = [
    // installed as composer dependency: %ROOT%/vendor/bin/submod
    __DIR__.'/../autoload.php',

    // installed as main package: %ROOT%/bin/submod
    __DIR__.'/../vendor/autoload.php'
];
foreach ($files as $file) {
    if (file_exists($file)) {
        require $file;
        break;
    }
}

// script needs to handle huge files (e.g. bluray rips with 16GB)
ini_set('memory_limit', -1);

$application = new \Mihaeu\Console\SubCollectorApplication();
$application->add(new \Mihaeu\Console\DownloadCommand());
$application->run();
