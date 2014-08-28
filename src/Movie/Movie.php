<?php

namespace Mihaeu\SubCollector\Movie;

/**
 * Represents a movie file.
 *
 * @package Mihaeu\Movie
 * @author Michael Haeuslmann <haeuslmann@gmail.com>
 */
class Movie extends \Mihaeu\SubCollector\File
{
    /**
     * @var \SplFileObject
     */
    private $subtitleFile = null;

    /**
     * @var string
     */
    private $movieName;

    /**
     * @param string $movieFile
     */
    public function __construct($movieFile)
    {
        parent::__construct($movieFile);
        $this->movieName = $this->getName();

        $subtitleFile = $this->movieFile->getPath().DIRECTORY_SEPARATOR.$this->movieName.'.srt';
        if (file_exists($subtitleFile)) {
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
    public function getName()
    {
        return $this->movieFile->getBasename('.'.$this->movieFile->getExtension());
    }
}
