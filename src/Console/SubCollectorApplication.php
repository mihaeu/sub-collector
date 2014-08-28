<?php

namespace Mihaeu\SubCollector\Console;

use Symfony\Component\Console\Application;

/**
 * Class SubCollectorApp
 *
 * @package Mihaeu
 * @author Michael Haeuslmann <haeuslmann@gmail.com>
 */
class SubCollectorApplication extends Application
{
    public function __construct()
    {
        parent::__construct('sub-collector');

        $this->add(new DownloadCommand());
        $this->add(new CleanCommand());
    }

    public function getHelp()
    {
        return <<<EOT
<question>
   _____       _            _____      _ _           _
  / ____|     | |          / ____|    | | |         | |
 | (___  _   _| |__ ______| |     ___ | | | ___  ___| |_ ___  _ __
  \___ \| | | | '_ |______| |    / _ \| | |/ _ \/ __| __/ _ \| '__|
  ____) | |_| | |_) |     | |___| (_) | | |  __| (__| || (_) | |
 |_____/ \__,_|_.__/       \_____\___/|_|_|\___|\___|\__\___/|_|

Batch-download subtitles for your movie collection.</question>

EOT;
    }
}
