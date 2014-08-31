<?php

namespace Mihaeu\SubCollector\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class DownloadCommand
 * @package Mihaeu
 * @author Michael Haeuslmann <haeuslmann@gmail.com>
 */
class DownloadCleanCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('download-clean')
            ->setDescription('Download subtitles for all movies found in a folder and clean them up.')
            ->addArgument(
                'path',
                InputArgument::REQUIRED,
                'Path to the folder that contains the movie files.'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $downloadCommand = $this->getApplication()->find('download');
        $downloadCommand->execute($input, $output);

        $cleanCommand = $this->getApplication()->find('clean');
        $cleanCommand->execute($input, $output);
    }
}
