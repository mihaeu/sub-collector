<?php

namespace Mihaeu\Movie;

/**
 * Represents a movie file.
 *
 * @package Mihaeu\Movie
 * @author Michael Haeuslmann <haeuslmann@gmail.com>
 */
class File
{
    /**
     * @var \SplFileObject
     */
    private $file;

    /**
     * Constructor.
     */
    public function __construct($file)
    {
       $file = new \SplFileObject($file);
    }


}
