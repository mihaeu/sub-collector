<?php

namespace Mihaeu\Subtitle;

/**
 * Searches and identifies subtitle files.
 *
 * @package Mihaeu\Movie
 */
class Finder extends \Mihaeu\FinderBase
{
    public function __construct($directory, array $fileExtensions = array())
    {
        $this->setDirectory($directory);

        if ( empty($fileExtensions))
        {
            $fileExtensions = array(
                'srt'
            );
        }
        $this->setFileExtensions($fileExtensions);

        $this->setCreateObject('\Mihaeu\Subtitle\Subtitle');
    }
}
