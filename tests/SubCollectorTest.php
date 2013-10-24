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
        $subtitle = $subCollector->downloadSubtitle(\Mockery::mock('\Mihaeu\Movie\Movie'));
        $this->assertNotEmpty($subtitle);
    }

    public function testSubtitleCannotBeDownloadedIfItDoesNotExist()
    {
        $mock = \Mockery::mock('\Mihaeu\Provider\SubProviderInterface');
        $mock->shouldReceive('createMovieHashFromMovieFile')
            ->andReturn(false);
        $subCollector = new \Mihaeu\SubCollector($mock);
        $subtitle = $subCollector->downloadSubtitle(\Mockery::mock('\Mihaeu\Movie\Movie'));
        $this->assertFalse($subtitle);
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
        $this->assertFalse(file_exists($fakeSubtitle), 'Subtitle already exists.');

        // fetch sub
        $fakeMovieWithoutSubtitle = new Mihaeu\Movie\Movie(
            vfsStream::url('testDir').DIRECTORY_SEPARATOR.'movies'.DIRECTORY_SEPARATOR.'Die Hard.mkv'
        );
        $this->assertTrue($subCollector->addSubtitleToMovie($fakeMovieWithoutSubtitle), 'Subtitle was not downloaded.');

        // sub should exist
        $this->assertTrue(file_exists($fakeSubtitle), 'Subtitle file does not exist after downloading.');
    }

    public function testSubtitleFileWillOnlyBeSavedWhenSubtitleWasFound()
    {
        // mock the sub provider
        $mock = \Mockery::mock('\Mihaeu\Provider\SubProviderInterface');
        $mock->shouldReceive('createMovieHashFromMovieFile')
            ->andReturn(false);

        $subCollector = new \Mihaeu\SubCollector($mock);
        $fakeMovieWithoutSubtitle = new Mihaeu\Movie\Movie(
            vfsStream::url('testDir').DIRECTORY_SEPARATOR.'movies'.DIRECTORY_SEPARATOR.'Die Hard.mkv'
        );
        $this->assertFalse($subCollector->addSubtitleToMovie($fakeMovieWithoutSubtitle));
    }
}
