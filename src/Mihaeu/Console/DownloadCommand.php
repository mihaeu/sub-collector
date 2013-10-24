<?php

namespace Mihaeu\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class DownloadCommand
 * @package Mihaeu
 * @author Michael Haeuslmann <haeuslmann@gmail.com>
 */
class DownloadCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('download')
            ->setDescription('Download subtitles for all movies found in a folder.')
            ->addArgument(
                'path',
                InputArgument::REQUIRED,
                'Path to the folder that contains the movie files.'
            )
            ->addOption(
                'yell',
                null,
                InputOption::VALUE_NONE,
                'If set, the task will yell in uppercase letters'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $subCollector = new \Mihaeu\SubCollector(new \Mihaeu\Provider\SubDBSubProvider());
        $movieFinder = new \Mihaeu\Movie\Finder($input->getArgument('path'));
        $movies = $movieFinder->findMoviesInFolder();
        foreach ($movies as $movie)
        {
            if ($movieFinder->movieHasNoSubtitle($movie))
            {
                $subtitleHasBeenDownloaded = $subCollector->addSubtitleToMovie($movie);
                if ($subtitleHasBeenDownloaded)
                {
                    $output->writeln( '<info>Found subtitle for '.$movie.'</info>');
                }
                else
                {
                    $output->writeln('<comment>No exact match found for '.$movie.'</comment>');
                }
            }
            else
            {
                $output->writeln('<comment>'.$movie.' already has a subtitle.</comment>');
            }
        }
    }
}
