<?php

namespace Mihaeu\SubCollector\Tests\Movie;

use Mihaeu\SubCollector\Movie\Finder;
use Mihaeu\SubCollector\Movie\Movie;

use org\bovigo\vfs\vfsStream;

/**
 * Class FinderTest
 *
 * The Finder relies heavily on the filesystem, which is being mocked by vfs:://stream for testing purposes.
 *
 * @author Michael Haeuslmann <haeuslmann@gmail.com>
 */
class FinderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Finder
     */
    private $movieFinder;

    public function setUp()
    {
        // mock the filesystem
        $testFiles = array(
            'movies' => array(
                'Armageddon.avi' => '',
                'Armageddon.srt' => '',
                'Die Hard.mkv' => '',
                'subFolderA' => array(
                    'subFolderB' => array(
                        'subFolderC' => array(
                            'Avatar.mp4' => '',
                            'Avatar.srt' => ''
                        )
                    )
                )
            )
        );
        $this->root = vfsStream::setup('testDir', null, $testFiles);
        $this->movieFinder = new Finder(vfsStream::url('testDir'));
    }

    public function testOnlyAcceptsDirectories()
    {
        $fakeMovie = vfsStream::url('testDir').
            DIRECTORY_SEPARATOR.'movies'.
            DIRECTORY_SEPARATOR.'Armageddon.avi';
        $this->setExpectedException('InvalidArgumentException');
        $this->movieFinder->setDirectory($fakeMovie);
    }

    public function testOnlyMoviesAreFoundInAFolder()
    {
        $movies = $this->movieFinder->findMoviesInFolder();
        $this->assertEquals(3, count($movies));
    }

    public function testMoviesCanBeNestedDeeplyInsideAFolder()
    {
        $fakePath = vfsStream::url('testDir').DIRECTORY_SEPARATOR.'movies'.DIRECTORY_SEPARATOR.'subFolderA';
        $this->movieFinder->setDirectory($fakePath);

        $movies = $this->movieFinder->findMoviesInFolder();
        $this->assertEquals(1, count($movies));
    }

    public function testMovieWithSubtitleWillBeDetected()
    {
        $fakeMovieWithSubtitle = vfsStream::url('testDir').DIRECTORY_SEPARATOR.'movies'.DIRECTORY_SEPARATOR.'Armageddon.avi';
        $movie = new Movie($fakeMovieWithSubtitle);
        $this->assertTrue($movie->hasSubtitle());
    }

    public function testMovieWithoutSubtitleWillBeDetected()
    {
        $fakeMovieWithoutSubtitle = vfsStream::url('testDir').DIRECTORY_SEPARATOR.'movies'.DIRECTORY_SEPARATOR.'Die Hard.mkv';
        $movie = new Movie($fakeMovieWithoutSubtitle);
        $this->assertTrue($movie->hasNoSubtitle());
    }

    public function testCustomFileExtensionsWillBeDetected()
    {
        // for testing purposes subtitles will be treated as custom movies (content makes no difference)
        // if subtitles are going to be detected, so will movies
        $this->movieFinder = new Finder(vfsStream::url('testDir'), array('srt'));
        $movies = $this->movieFinder->findMoviesInFolder();
        $this->assertEquals(2, count($movies));
    }
}
