<?php

namespace Mihaeu\Movie;

/**
 * Searches and identifies movie files.
 *
 * @package Mihaeu\Movie
 * @author Michael Haeuslmann <haeuslmann@gmail.com>
 */
class Finder
{
    /**
     * @var string
     */
    private $directory;

    /**
     * @var array
     */
    private $movieFileExtensions = ['mov', 'mkv', 'avi', 'mp4', 'mpg', 'mpeg', 'mts', 'flv', 'wmv'];

    /**
     * Constructor.
     */
    public function __construct($directory, array $movieFileExtensions = [])
    {
        $this->setDirectory($directory);

        if ( ! empty($movieFileExtensions))
        {
            $this->movieFileExtensions = $movieFileExtensions;
        }
    }

    /**
     * Finds all files in a folder.
     *
     * @return array
     */
    public function findMoviesInFolder()
    {
        $fileIterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($this->directory),
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
            $fileExtensionRegex = '/\.('.implode(')|(', $this->movieFileExtensions).')$/';
            return preg_match($fileExtensionRegex, $file);
        });
    }

    /**
     * Checks if a movie already has a subtitle (filename with the same basename,
     * but .srt extension)
     *
     * @param   string $movie
     * @return  bool
     */
    public function movieHasSubtitle($movie)
    {
        $subtitleFilename = dirname($movie).DIRECTORY_SEPARATOR.$this->generateSubtitleName($movie);
        return file_exists($subtitleFilename);
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
        return ! $this->movieHasSubtitle($movie);
    }

    /**
     * Generates subtitle name from a movie file (same name, but .srt extension).
     *
     * @param $movie
     * @return mixed
     */
    public function generateSubtitleName($movie)
    {
        return preg_replace('/\.\w+$/', '.srt', basename($movie));
    }

    /**
     * @param string $directory
     */
    public function setDirectory($directory)
    {
        if ( ! is_dir($directory))
        {
            throw new \InvalidArgumentException($directory.' is not a directory.');
        }
        $this->directory = $directory;
    }
}
