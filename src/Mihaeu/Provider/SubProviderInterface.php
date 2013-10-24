<?php

namespace Mihaeu\Provider;

/**
 * Class SubProviderInterface.php
 *
 * @author Michael Haeuslmann <haeuslmann@gmail.com>
 */
interface SubProviderInterface
{
    public function createMovieHashFromMovieFile(\Mihaeu\Movie\Movie $movie);

    public function downloadSubtitleByHash($hash);
}
