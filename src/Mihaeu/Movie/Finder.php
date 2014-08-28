<?php

namespace Mihaeu\Movie;

/**
 * Searches and identifies movie files.
 *
 * @package Mihaeu\Movie
 * @author Michael Haeuslmann <haeuslmann@gmail.com>
 */
class Finder extends \Mihaeu\FinderBase
{
    public function __construct($directory, array $fileExtensions = array())
    {
        $this->setDirectory($directory);

        if ( empty($fileExtensions))
        {
            $fileExtensions = array(
                'mov', 'mkv', 'avi', 'mp4', 'mpg',
                'mpeg', 'mts', 'flv', 'wmv'
            );
        }
        $this->setFileExtensions($fileExtensions);

        $this->setCreateObject('\Mihaeu\Movie\Movie');
    }
}
