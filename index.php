<?php 

// script needs to handle huge files (e.g. bluray rips with 16GB)
ini_set('memory_limit', -1);

// Go ...
echo '----------------------------------------'.PHP_EOL. 
	 'Looking for movies without subtitles ...'.PHP_EOL.
     '----------------------------------------'.PHP_EOL.PHP_EOL;

$movies = findMoviesInFolder('/media/media/videos/movies');
foreach ($movies as $movie)
{
	if (movieHasNoSubtitle($movie))
	{
		addSubtitleToMovie($movie);
	}
}







/**
 * Download the subtitle for a movie from the SubDB API.
 *
 * @param  string $filename
 * @return string
 */
function downloadSubtitle($filename)
{
	// block size which is required for the API call
	$READ_SIZE = 64*1024;
	
	// for the file pointer
	$filesize = filesize($filename);

	// open file handle
	$handle = fopen($filename, 'r');

	// read first part
	$data = fread($handle, $READ_SIZE);

	// move the file pointer ahead, because
	// we only need the first and the last
	// 64KB of the video file
	fseek($handle, -$READ_SIZE, SEEK_END);

	// read the last part and concat
	$data .= fread($handle, $READ_SIZE);

	// close the handle
	fclose($handle);

	// create the hash for subDB
	$movieHash = md5($data);

	$apiUrl = 'http://api.thesubdb.com/';
	// $query = '?action=search&hash='.$movieHash;
	$query = '?action=download&hash='.$movieHash.'&language=en';
	$curlHandle = curl_init($apiUrl.$query);
	curl_setopt_array($curlHandle, [
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_USERAGENT => 'SubDB/1.0 (MovieManager/1.0; http://mike-dev.info)',
		// CURLOPT_VERBOSE => 1,
		// CURLOPT_HEADER => 1
	]);
	$curlResponse = curl_exec($curlHandle);
	curl_close($curlHandle);

	return $curlResponse;
}

/**
 * 
 *
 * @param  string $folder
 * @return array
 */
function findMoviesInFolder($folder)
{
	if ( ! file_exists($folder) || ! is_dir($folder))
	{
		throw new Exception("No folder.");
	}

	$fileIterator = new RecursiveIteratorIterator(
				new RecursiveDirectoryIterator($folder),
				RecursiveIteratorIterator::SELF_FIRST
	);
	$files = [];
	foreach ($fileIterator as $key => $value)
	{
		$files[] = $key;
	}

	// filter only movie files
	return array_filter($files, function($file)
	{
		return preg_match('/\.(avi)|(mp4)|(mpeg)|(mpg)|(m4v)|(mkv)$/', $file);
	});
}

/**
 * Downloads and saves the subtitle to the movie.
 *
 * @param string movie
 */
function addSubtitleToMovie($movie)
{
	$subtitleFilename = preg_replace('/\.\w+$/', '.srt', basename($movie));
	$movieTitle = basename($subtitleFilename, '.srt');

	// fetch subtitle
	$subtitle = downloadSubtitle($movie);

	if (empty($subtitle))
	{
		echo 'No exact match found for '.$movieTitle.' on the SubDB database.'.PHP_EOL;
		return;
	}

	// get movie folder
	$movieFolder = dirname($movie);

	// save subtitle
	$subtitlePath = $movieFolder.'/'.$subtitleFilename;
	file_put_contents($subtitlePath, $subtitle);
	echo 'Found subtitle for '.$movieTitle.' and saved it to '.$subtitlePath.' ('.filesize($subtitlePath).' bytes)'.PHP_EOL;
}

/**
 * Checks if a movie already has a subtitle (filename with the same basename,
 * but .srt extension)
 *
 * @param   string $movie
 * @return  bool
 */
function movieHasNoSubtitle($movie)
{
	$subtitleFilename = preg_replace('/\.\w+$/', '.srt', basename($movie));
	$movieTitle = basename($subtitleFilename, '.srt');

	// if the movie already has a subtitle, ship it
	if (file_exists(dirname($movie).'/'.$subtitleFilename))
	{
		echo 'Movie '.$movieTitle.' already has a subtitle.'.PHP_EOL;
		return false;
	}
	return true;
}
