<?php

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;

class FinderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mihaeu\Movie\Finder
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
        $this->movieFinder = new Mihaeu\Movie\Finder(vfsStream::url('testDir'));
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
        $this->assertTrue($this->movieFinder->movieHasSubtitle($fakeMovieWithSubtitle));
    }

    public function testMovieWithoutSubtitleWillBeDetected()
    {
        $fakeMovieWithoutSubtitle = vfsStream::url('testDir').DIRECTORY_SEPARATOR.'movies'.DIRECTORY_SEPARATOR.'Die Hard.mkv';
        $this->assertTrue($this->movieFinder->movieHasNoSubtitle($fakeMovieWithoutSubtitle));
    }
}
