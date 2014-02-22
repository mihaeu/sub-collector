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
    private $movieFileExtensions = array(
        'mov', 'mkv', 'avi', 'mp4', 'mpg',
        'mpeg', 'mts', 'flv', 'wmv'
    );

    /**
     * Constructor.
     */
    public function __construct($directory, array $movieFileExtensions = array())
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
        $movies = array();
        foreach ($fileIterator as $key => $value)
        {
            if (is_dir($key))
            {
                continue;
            }

            $movieFile = new Movie($key);
            if (in_array($movieFile->getMovieFileExtension(), $this->movieFileExtensions))
            {
                $movies[] = $movieFile;
            }

        }
        return $movies;
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
