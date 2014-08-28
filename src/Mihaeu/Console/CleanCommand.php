<?php

namespace Mihaeu\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use \Mihaeu\Provider\SubProviderInterface;
use \Mihaeu\Provider\SubDBSubProvider;

/**
 * Class DownloadCommand
 * @package Mihaeu
 */
class CleanCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('clean')
            ->setDescription('Clean subtitles in a folder.')
            ->addArgument(
                'path',
                InputArgument::REQUIRED,
                'Path to the folder that contains the subtitle files.'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Cleaning subtitles in '.$input->getArgument('path').'</info>');

        $subCleaner = new \Mihaeu\Subtitle\Cleaner($output);
        $subFinder = new \Mihaeu\Subtitle\Finder($input->getArgument('path'));
        $subs = $subFinder->findFilesInFolder();
        foreach ($subs as $sub)
        {
            $output->writeln( '<info>'. $sub->getFileName() .'</info>');
            $subCleaner->cleanUpFile($sub->getFileName(), $sub->getFileName());
        }
    }
}
