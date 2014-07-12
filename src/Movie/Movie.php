<?php

namespace Mihaeu\SubCollector\Movie;

/**
 * Represents a movie file.
 *
 * @package Mihaeu\Movie
 * @author Michael Haeuslmann <haeuslmann@gmail.com>
 */
class Movie
{
    /**
     * @var \SplFileObject
     */
    private $movieFile;

    /**
     * @var \SplFileObject
     */
    private $subtitleFile = null;

    /**
     * @var string
     */
    private $movieFileExtension;

    /**
     * @var string
     */
    private $movieName;

    /**
     * Constructor.
     */
    public function __construct($movieFile)
    {
        $this->movieFile = new \SplFileObject($movieFile);
        $this->movieFileExtension = $this->movieFile->getExtension();
        $this->movieName = preg_replace('/\.[0-9a-zA-Z]+$/', '', $this->movieFile->getBasename());

        $subtitleFile = $this->movieFile->getPath().DIRECTORY_SEPARATOR.$this->movieName.'.srt';
        if (file_exists($subtitleFile))
        {
            $this->subtitleFile = new \SplFileObject($subtitleFile);
        }
    }

    /**
     * @return bool
     */
    public function hasSubtitle()
    {
        return $this->subtitleFile !== null;
    }

    /**
     * @return bool
     */
    public function hasNoSubtitle()
    {
        return $this->subtitleFile === null;
    }

    /**
     * @return string
     */
    public function getMovieFileExtension()
    {
        return $this->movieFileExtension;
    }

    /**
     * @return string
     */
    public function getMovieFilename()
    {
        return $this->movieFile->getPath().DIRECTORY_SEPARATOR.$this->movieFile->getBasename();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->movieFile->getBasename();
    }
}
