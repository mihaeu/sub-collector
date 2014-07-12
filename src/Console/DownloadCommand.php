<?php

namespace Mihaeu\SubCollector\Console;

use Mihaeu\SubCollector\Movie\Finder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Mihaeu\SubCollector\Provider\SubProviderInterface;
use Mihaeu\SubCollector\Provider\SubDBSubProvider;
use Mihaeu\SubCollector\SubCollector;
use Mihaeu\SubCollector\Movie\Movie;

/**
 * Class DownloadCommand
 * @package Mihaeu
 * @author Michael Haeuslmann <haeuslmann@gmail.com>
 */
class DownloadCommand extends Command
{
    /**
     * @var SubProviderInterface
     */
    private $subProvider;

    public function __construct( $subProvider = null)
    {
        $this->subProvider = $subProvider;
        if ($subProvider === null) {
            $this->subProvider = new SubDBSubProvider();
        }

        parent::__construct();
    }

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
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $subCollector = new SubCollector($this->subProvider);
        $movieFinder = new Finder($input->getArgument('path'));
        $movies = $movieFinder->findMoviesInFolder();

        /** @var Movie $movie */
        foreach ($movies as $movie) {
            if ($movie->hasNoSubtitle()) {
                $subtitleHasBeenDownloaded = $subCollector->addSubtitleToMovie($movie);
                if ($subtitleHasBeenDownloaded) {
                    $output->writeln( '<info>Downloaded subtitle for '.$movie->getName().'</info>');
                } else {
                    $output->writeln('<comment>No exact match found for '.$movie->getName().'</comment>');
                }
            } else {
                $output->writeln('<comment>'.$movie->getName().' already has a subtitle.</comment>');
            }
        }
    }
}
