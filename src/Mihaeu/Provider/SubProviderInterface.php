<?php

namespace Mihaeu\Provider;

/**
 * Class SubProviderInterface.php
 *
 * @author Michael Haeuslmann <haeuslmann@gmail.com>
 */
interface SubProviderInterface
{
    public function createMovieHashFromMovieFile($movieFile);

    public function downloadSubtitleByHash($hash);
}
