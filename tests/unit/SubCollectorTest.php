<?php

namespace Mihaeu\SubCollector\Tests;

use Mihaeu\SubCollector\Movie\Movie;
use Mihaeu\SubCollector\SubCollector;
use Mihaeu\SubCollector\Provider\SubProviderInterface;

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;

/**
 * Class SubCollectorTest
 *
 * The SubCollector requires both the filesystem and the Providers to be mocked.
 *
 * @author Michael Haeuslmann <haeuslmann@gmail.com>
 */
class SubCollectorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SubCollector
     */
    private $subCollector;

    /**
     * @var SubProviderInterface
     */
    private $mockSubProvider;

    /**
     * @var  vfsStreamDirectory
     */
    private $root;

    public function setUp()
    {
        $this->mockSubProvider = \Mockery::mock('\Mihaeu\SubCollector\Provider\SubDBSubProvider');
        $this->subCollector = new SubCollector($this->mockSubProvider);

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
        $mock = \Mockery::mock('\Mihaeu\SubCollector\Provider\SubProviderInterface');
        $mock->shouldReceive('createMovieHashFromMovieFile')
            ->andReturn('hash');
        $mock->shouldReceive('downloadSubtitleByHash')
            ->andReturn('a subtitle');

        $subCollector = new SubCollector($mock);
        $subtitle = $subCollector->downloadSubtitle(\Mockery::mock('\Mihaeu\SubCollector\Movie\Movie'));
        $this->assertNotEmpty($subtitle);
    }

    public function testSubtitleCannotBeDownloadedIfItDoesNotExist()
    {
        $mock = \Mockery::mock('\Mihaeu\SubCollector\Provider\SubProviderInterface');
        $mock->shouldReceive('createMovieHashFromMovieFile')
            ->andReturn(false);
        $subCollector = new SubCollector($mock);
        $subtitle = $subCollector->downloadSubtitle(\Mockery::mock('\Mihaeu\SubCollector\Movie\Movie'));
        $this->assertFalse($subtitle);
    }

    public function testDownloadedSubtitleWillBeSavedAsASrtFile()
    {
        // mock the sub provider
        $mock = \Mockery::mock('\Mihaeu\SubCollector\Provider\SubProviderInterface');
        $mock->shouldReceive('createMovieHashFromMovieFile')
            ->andReturn('hash');
        $mock->shouldReceive('downloadSubtitleByHash')
            ->andReturn('a subtitle');
        $subCollector = new SubCollector($mock);

        // sub doesnt exist before
        $fakeSubtitle = vfsStream::url('testDir').DIRECTORY_SEPARATOR.'movies'.DIRECTORY_SEPARATOR.'Die Hard.srt';
        $this->assertFalse(file_exists($fakeSubtitle), 'Subtitle already exists.');

        // fetch sub
        $fakeMovieWithoutSubtitle = new Movie(
            vfsStream::url('testDir').DIRECTORY_SEPARATOR.'movies'.DIRECTORY_SEPARATOR.'Die Hard.mkv'
        );
        $this->assertTrue($subCollector->addSubtitleToMovie($fakeMovieWithoutSubtitle), 'Subtitle was not downloaded.');

        // sub should exist
        $this->assertTrue(file_exists($fakeSubtitle), 'Subtitle file does not exist after downloading.');
    }

    public function testSubtitleFileWillOnlyBeSavedWhenSubtitleWasFound()
    {
        // mock the sub provider
        $mock = \Mockery::mock('\Mihaeu\SubCollector\Provider\SubProviderInterface');
        $mock->shouldReceive('createMovieHashFromMovieFile')
            ->andReturn(false);

        $subCollector = new SubCollector($mock);
        $fakeMovieWithoutSubtitle = new Movie(
            vfsStream::url('testDir').DIRECTORY_SEPARATOR.'movies'.DIRECTORY_SEPARATOR.'Die Hard.mkv'
        );
        $this->assertFalse($subCollector->addSubtitleToMovie($fakeMovieWithoutSubtitle));
    }
}
