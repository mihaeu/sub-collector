<?php

namespace Mihaeu\SubCollector\Tests\Console;

use org\bovigo\vfs\vfsStream;
use Symfony\Component\Console\Tester\CommandTester;

use Mihaeu\SubCollector\Console\SubCollectorApplication;
use Mihaeu\SubCollector\Console\DownloadCommand;

/**
 * DownloadCommandTest
 *
 * Dependencies for the Movie\Finder and the Provider\SubProvider classes are being
 * mocked to avoid side-effects and to allow everyone to run these tests.
 *
 * @author Michael Haeuslmann <haeuslmann@gmail.com>
 */
class DownloadCommandTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        // mock the filesystem
        $testFiles = array(
            'movieWithSubtitle' => array(
                'Armageddon.avi' => '',
                'Armageddon.srt' => ''
            ),
            'movieWithoutSubtitle' => array(
                'Die Hard.mkv' => ''
            )
        );
        vfsStream::setup('testDir', null, $testFiles);
    }

    public function testDownloadingOfSubtitlesForAMovieWithoutSubtitleAndExistingSubtitleOnServer()
    {
        $mockedProvider = \Mockery::mock('\Mihaeu\SubCollector\Provider\SubProviderInterface');
        $mockedProvider
            ->shouldReceive('createMovieHashFromMovieFile')
            ->andReturn('hash');
        $mockedProvider
            ->shouldReceive('downloadSubtitleByHash')
            ->andReturn('sub');
        $application = new SubCollectorApplication();
        $application->add(new DownloadCommand($mockedProvider));

        $command = $application->find('download');
        $commandTester = new CommandTester($command);

        $movieFolderWithoutSubtitle = vfsStream::url('testDir').DIRECTORY_SEPARATOR.'movieWithoutSubtitle';
        $commandTester->execute(array('command' => $command->getName(), 'path' => $movieFolderWithoutSubtitle));
        $this->assertRegExp('/Downloaded subtitle for /', $commandTester->getDisplay());
    }

    public function testDownloadOfSubtitlesForMovieWithoutSubtitleButNoSubtitleOnServer()
    {
        $mockedProvider = \Mockery::mock('\Mihaeu\SubCollector\Provider\SubProviderInterface');
        $mockedProvider
            ->shouldReceive('createMovieHashFromMovieFile')
            ->andReturn('hash');
        $mockedProvider
            ->shouldReceive('downloadSubtitleByHash')
            ->andReturn('');
        $application = new SubCollectorApplication();
        $application->add(new DownloadCommand($mockedProvider));

        $command = $application->find('download');
        $commandTester = new CommandTester($command);

        $movieFolderWithoutSubtitle = vfsStream::url('testDir').DIRECTORY_SEPARATOR.'movieWithoutSubtitle';
        $commandTester->execute(array('command' => $command->getName(), 'path' => $movieFolderWithoutSubtitle));
        $this->assertRegExp('/No exact match found for/', $commandTester->getDisplay());
    }

    public function testDownloadingOfSubtitlesForAMovieWhichHasSubtitlesIsSkipped()
    {
        $application = new SubCollectorApplication();
        $application->add(new DownloadCommand());
        $command = $application->find('download');
        $commandTester = new CommandTester($command);

        $movieFolderWithSubtitle = vfsStream::url('testDir').DIRECTORY_SEPARATOR.'movieWithSubtitle';
        $commandTester->execute(array('command' => $command->getName(), 'path' => $movieFolderWithSubtitle));
        $this->assertRegExp('/already has a subtitle/', $commandTester->getDisplay());
    }
}
