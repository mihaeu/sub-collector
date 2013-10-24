<?php

use Symfony\Component\Console\Tester\CommandTester;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;

use Mihaeu\Console\SubCollectorApplication;
use Mihaeu\Console\DownloadCommand;

class DownloadCommandTest extends PHPUnit_Framework_TestCase
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
        $mockedProvider = \Mockery::mock('\Mihaeu\Provider\SubProviderInterface');
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
        $this->assertRegExp('/.../', $commandTester->getDisplay());
    }

    public function testDownloadOfSubtitlesForMovieWithoutSubtitleButNoSubtitleOnServer()
    {
        $mockedProvider = \Mockery::mock('\Mihaeu\Provider\SubProviderInterface');
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
        $this->assertRegExp('/.../', $commandTester->getDisplay());
    }

    public function testDownloadingOfSubtitlesForAMovieWhichHasSubtitlesIsSkipped()
    {
        $application = new SubCollectorApplication();
        $application->add(new DownloadCommand());
        $command = $application->find('download');
        $commandTester = new CommandTester($command);

        $movieFolderWithSubtitle = vfsStream::url('testDir').DIRECTORY_SEPARATOR.'movieWithSubtitle';
        $commandTester->execute(array('command' => $command->getName(), 'path' => $movieFolderWithSubtitle));
        $this->assertRegExp('/.../', $commandTester->getDisplay());
    }
}
