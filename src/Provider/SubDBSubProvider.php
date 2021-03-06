<?php

namespace Mihaeu\SubCollector\Provider;

use Mihaeu\SubCollector\Movie\Movie;

/**
 * Implementation for the SubDB API.
 *
 * @package Mihaeu
 * @author Michael Haeuslmann <haeuslmann@gmail.com>
 */
class SubDBSubProvider implements SubProviderInterface
{
    const API_URL = 'http://api.thesubdb.com/';
    const API_USERAGENT = 'SubDB/1.0 (MovieManager/1.0; http://mike-dev.info)';

    public function createMovieHashFromMovieFile(Movie $movie)
    {
        // block size which is required for the API call
        $READ_SIZE = 64 * 1024;

        // open file handle
        $handle = fopen($movie->getFilename(), 'r');

        // read first part
        $data = fread($handle, $READ_SIZE);

        // move the file pointer ahead, because we only need the first and the last
        // 64KB of the video file
        fseek($handle, -$READ_SIZE, SEEK_END);

        // read the last part and concat
        $data .= fread($handle, $READ_SIZE);

        // close the handle
        fclose($handle);

        return md5($data);
    }

    public function downloadSubtitleByHash($hash)
    {
        $query = '?action=download&hash='.$hash.'&language=en';
        $curlHandle = curl_init(self::API_URL.$query);
        curl_setopt_array($curlHandle, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERAGENT => self::API_USERAGENT,
        ));
        $curlResponse = curl_exec($curlHandle);
        curl_close($curlHandle);

        return $curlResponse;
    }
}
