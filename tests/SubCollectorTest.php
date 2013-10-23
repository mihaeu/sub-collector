<?php

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;

class SubCollectorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mihaeu\SubCollector
     */
    private $subCollector;

    /**
     * @var Mihaeu\Provider\SubProviderInterface
     */
    private $mockSubProvider;

    /**
     * @var  vfsStreamDirectory
     */
    private $root;

    private $testDir;

    public function setUp()
    {
        $this->mockSubProvider = \Mockery::mock('\Mihaeu\Provider\SubDBSubProvider');
        $this->subCollector = new \Mihaeu\SubCollector($this->mockSubProvider);

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
    }

    public function testSubtitleCanBeDownloadedIfExists()
    {
        // mock sub provider
        $mock = \Mockery::mock('\Mihaeu\Provider\SubProviderInterface');
        $mock->shouldReceive('createMovieHashFromMovieFile')
            ->andReturn('hash');
        $mock->shouldReceive('downloadSubtitleByHash')
            ->andReturn('a subtitle');

        $subCollector = new \Mihaeu\SubCollector($mock);
        $subtitle = $subCollector->downloadSubtitle('');
        $this->assertNotEmpty($subtitle);
    }

    public function testSubtitleCannotBeDownloadedIfItDoesNotExist()
    {
        $mock = \Mockery::mock('\Mihaeu\Provider\SubProviderInterface');
        $mock->shouldReceive('createMovieHashFromMovieFile')
            ->andReturn(false);
        $subCollector = new \Mihaeu\SubCollector($mock);
        $subtitle = $subCollector->downloadSubtitle('');
        $this->assertFalse($subtitle);
    }

    public function testOnlyMoviesAreFoundInAFolder()
    {
        $movies = $this->subCollector->findMoviesInFolder(vfsStream::url('testDir'));
        $this->assertEquals(3, count($movies));

        // rejects files
        $fakeMovie = vfsStream::url('testDir').DIRECTORY_SEPARATOR.'movies'.DIRECTORY_SEPARATOR.'Armageddon.avi';
        $this->assertEquals([], $this->subCollector->findMoviesInFolder($fakeMovie));
    }

    public function testMoviesCanBeNestedDeeplyInsideAFolder()
    {
        $fakePath = vfsStream::url('testDir').DIRECTORY_SEPARATOR.'movies'.DIRECTORY_SEPARATOR.'subFolderA';
        $movies = $this->subCollector->findMoviesInFolder($fakePath);
        $this->assertEquals(1, count($movies));
    }

    public function testMovieWithSubtitleWillBeDetected()
    {
        $fakeMovieWithSubtitle = vfsStream::url('testDir').DIRECTORY_SEPARATOR.'movies'.DIRECTORY_SEPARATOR.'Armageddon.avi';
        $this->assertTrue($this->subCollector->movieHasSubtitle($fakeMovieWithSubtitle));
    }

    public function testMovieWithoutSubtitleWillBeDetected()
    {
        $fakeMovieWithoutSubtitle = vfsStream::url('testDir').DIRECTORY_SEPARATOR.'movies'.DIRECTORY_SEPARATOR.'Die Hard.mkv';
        $this->assertTrue($this->subCollector->movieHasNoSubtitle($fakeMovieWithoutSubtitle));
    }

    public function testDownloadedSubtitleWillBeSavedAsASrtFile()
    {
        // mock the sub provider
        $mock = \Mockery::mock('\Mihaeu\Provider\SubProviderInterface');
        $mock->shouldReceive('createMovieHashFromMovieFile')
            ->andReturn('hash');
        $mock->shouldReceive('downloadSubtitleByHash')
            ->andReturn('a subtitle');
        $subCollector = new \Mihaeu\SubCollector($mock);

        // sub doesnt exist before
        $fakeSubtitle = vfsStream::url('testDir').DIRECTORY_SEPARATOR.'movies'.DIRECTORY_SEPARATOR.'Die Hard.srt';
        $this->assertFalse(file_exists($fakeSubtitle));

        // fetch sub
        $fakeMovieWithoutSubtitle = vfsStream::url('testDir').DIRECTORY_SEPARATOR.'movies'.DIRECTORY_SEPARATOR.'Die Hard.mkv';
        $this->assertTrue($subCollector->addSubtitleToMovie($fakeMovieWithoutSubtitle));

        // sub should exist
        $this->assertTrue(file_exists($fakeSubtitle));
    }

    public function testSubtitleFileWillOnlySavedWhenSubtitleWasFound()
    {
        // mock the sub provider
        $mock = \Mockery::mock('\Mihaeu\Provider\SubProviderInterface');
        $mock->shouldReceive('createMovieHashFromMovieFile')
            ->andReturn(false);

        $subCollector = new \Mihaeu\SubCollector($mock);
        $fakeMovieWithoutSubtitle = vfsStream::url('testDir').DIRECTORY_SEPARATOR.'movies'.DIRECTORY_SEPARATOR.'Die Hard.mkv';
        $this->assertFalse($subCollector->addSubtitleToMovie($fakeMovieWithoutSubtitle));
    }
}
