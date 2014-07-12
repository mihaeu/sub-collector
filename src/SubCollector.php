<?php

namespace Mihaeu\SubCollector;

use Mihaeu\SubCollector\Movie\Movie;
use Mihaeu\SubCollector\Provider\SubProviderInterface;

/**
 * This class is the main an orchestrates the different components.
 *
 * @package Mihaeu
 * @author Michael Haeuslmann <haeuslmann@gmail.com>
 */
class SubCollector
{
    /**
     * @var Provider\SubProviderInterface
     */
    private $subtitleProvider;

    /**
     * Constructor.
     *
     * @param SubProviderInterface $subtitleProvider
     */
    public function __construct(SubProviderInterface $subtitleProvider)
    {
        $this->subtitleProvider = $subtitleProvider;
    }

    /**
     * Download the subtitle for a movie from the SubDB API.
     *
     * @param  Movie       $movie
     * @return string|bool
     */
    public function downloadSubtitle(Movie $movie)
    {
        $hash = $this->subtitleProvider->createMovieHashFromMovieFile($movie);
        if (!$hash) {
            return false;
        }

        return $this->subtitleProvider->downloadSubtitleByHash($hash);
    }

    /**
     * Downloads and saves the subtitle to the movie.
     *
     * @param Movie $movie
     * @return bool
     */
    public function addSubtitleToMovie(Movie $movie)
    {
        // fetch subtitle
        $subtitle = $this->downloadSubtitle($movie);
        if (empty($subtitle)) {
            return false;
        }

        // save subtitle
        $subtitlePath = preg_replace('/\.\w+$/', '.srt', $movie->getMovieFilename());
        $bytesWritten = file_put_contents($subtitlePath, $subtitle);
        return $bytesWritten !== false;
    }
}
