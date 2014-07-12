<?php

namespace Mihaeu\SubCollector\Provider;

use Mihaeu\SubCollector\Movie\Movie;

/**
 * Class SubProviderInterface.php
 *
 * @author Michael Haeuslmann <haeuslmann@gmail.com>
 */
interface SubProviderInterface
{
    public function createMovieHashFromMovieFile(Movie $movie);

    public function downloadSubtitleByHash($hash);
}
