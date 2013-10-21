<?php

namespace Mihaeu;

/**
 * This class is the main an orchestrates the different components.
 *
 * @package Mihaeu
 * @author Michael Haeuslmann <haeuslmann@gmail.com>
 */
class SubCollector
{
    /**
     * @var SubProviderInterface
     */
    private $subtitleProvider;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->subtitleProvider = new Provider\SubDBSubProvider();
    }

    /**
     * Download the subtitle for a movie from the SubDB API.
     *
     * @param  string $filename
     * @return string
     */
    public function downloadSubtitle($filename)
    {
        $hash = $this->subtitleProvider->createMovieHashFromMovieFile($filename);
        return $this->subtitleProvider->downloadSubtitleByHash($hash);
    }

    /**
     *
     *
     * @param  string $folder
     * @return array
     */
    public function findMoviesInFolder($folder)
    {
        if ( ! file_exists($folder) || ! is_dir($folder))
        {
            throw new \Exception("No folder.");
        }

        $fileIterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($folder),
            \RecursiveIteratorIterator::SELF_FIRST
        );
        $files = [];
        foreach ($fileIterator as $key => $value)
        {
            $files[] = $key;
        }

        // filter only movie files
        return array_filter($files, function($file)
        {
            return preg_match('/\.(avi)|(mp4)|(mpeg)|(mpg)|(m4v)|(mkv)$/', $file);
        });
    }

    /**
     * Downloads and saves the subtitle to the movie.
     *
     * @param string $movie
     * @return bool
     */
    public function addSubtitleToMovie($movie)
    {
        $subtitleFilename = preg_replace('/\.\w+$/', '.srt', basename($movie));
        $movieTitle = basename($subtitleFilename, '.srt');

        // fetch subtitle
        $subtitle = $this->downloadSubtitle($movie);

        if (empty($subtitle))
        {
            return false;
        }

        // get movie folder
        $movieFolder = dirname($movie);

        // save subtitle
        $subtitlePath = $movieFolder.'/'.$subtitleFilename;
        file_put_contents($subtitlePath, $subtitle);
        return true;
    }

    /**
     * Checks if a movie already has a subtitle (filename with the same basename,
     * but .srt extension)
     *
     * @param   string $movie
     * @return  bool
     */
    public function movieHasNoSubtitle($movie)
    {
        $subtitleFilename = dirname($movie).'/'.preg_replace('/\.\w+$/', '.srt', basename($movie));
        return ! file_exists($subtitleFilename);
    }
}
