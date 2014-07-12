<?php

namespace Mihaeu\SubCollector;

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
     */
    public function __construct(Provider\SubProviderInterface $subtitleProvider)
    {
        $this->subtitleProvider = $subtitleProvider;
    }

    /**
     * Download the subtitle for a movie from the SubDB API.
     *
     * @param  Movie\Movie
     * @return string|bool
     */
    public function downloadSubtitle(Movie\Movie $movie)
    {
        $hash = $this->subtitleProvider->createMovieHashFromMovieFile($movie);
        if ( ! $hash)
        {
            return false;
        }

        return $this->subtitleProvider->downloadSubtitleByHash($hash);
    }

    /**
     * Downloads and saves the subtitle to the movie.
     *
     * @param Movie\Movie $movie
     * @return bool
     */
    public function addSubtitleToMovie(Movie\Movie $movie)
    {
        // fetch subtitle
        $subtitle = $this->downloadSubtitle($movie);
        if (empty($subtitle))
        {
            return false;
        }

        // save subtitle
        $subtitlePath = preg_replace('/\.\w+$/', '.srt', $movie->getMovieFilename());
        $bytesWritten = file_put_contents($subtitlePath, $subtitle);
        return $bytesWritten !== false;
    }
}
