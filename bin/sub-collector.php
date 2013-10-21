<?php

require __DIR__.'/../vendor/autoload.php';

// script needs to handle huge files (e.g. bluray rips with 16GB)
ini_set('memory_limit', -1);

// Go ...
echo '----------------------------------------'.PHP_EOL.
    'Looking for movies without subtitles ...'.PHP_EOL.
    '----------------------------------------'.PHP_EOL.PHP_EOL;

$subCollector = new Mihaeu\SubCollector();
$movies = $subCollector->findMoviesInFolder('/media/media/videos/movies');
foreach ($movies as $movie)
{
    if ($subCollector->movieHasNoSubtitle($movie))
    {
        $subCollector->addSubtitleToMovie($movie);
    }
}


