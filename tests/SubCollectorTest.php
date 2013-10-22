<?php

class SubCollectorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mihaeu\SubCollector
     */
    private $subCollector;

    public function setUp()
    {
        $this->subCollector = new Mihaeu\SubCollector();

        // set up folder structure
//        $testFolder = __DIR__.'/data';
//        mkdir($testFolder);
//
//        $movieFolder = $testFolder.'/movies';
//        mkdir($movieFolder);
//
//        $nestedMovieFolder = $movieFolder.'/a/b/c/d';
//        mkdir($nestedMovieFolder, 777, true);
//
//        // create dummy files
//        $dummyFiles = array(
//            $movieFolder.'/Die Hard.mkv',
//            $movieFolder.'/Armageddon.avi',
//            $movieFolder.'/Armageddon.srt',
//            $nestedMovieFolder.'/Avatar.mp4',
//            $nestedMovieFolder.'/Avatar.srt',
//        );
//        foreach ($dummyFiles as $dummyFile)
//        {
//            touch($dummyFile);
//        }
    }

    public function tearDown()
    {
//        $this->removeDirectoryIncludingContents(__DIR__.'/data');
    }

    public function testSubtitleCanBeDownloadedIfExists()
    {
        $subtitle = $this->subCollector->downloadSubtitle('/etc/passwd');
        $this->assertNotEmpty($subtitle);
    }

    public function testSubtitleCannotBeDownloadedIfItDoesNotExist()
    {
        $subtitle = $this->subCollector->downloadSubtitle(__DIR__);
        $this->assertEmpty($subtitle);
    }

    public function testOnlyMoviesAreFoundInAFolder()
    {
        $movies = $this->subCollector->findMoviesInFolder(__DIR__);
        $this->assertEquals(count($movies), 5);
    }

    public function testMoviesCanBeNestedDeeplyInsideAFolder()
    {
        $movies = $this->subCollector->findMoviesInFolder(__DIR__);
        $this->assertEquals(count($movies), 5);
    }

    public function testMovieWithSubtitleWillBeDetected()
    {
        $this->assertTrue($this->subCollector->movieHasNoSubtitle(__DIR__));
    }

    public function testDownloadedSubtitleWillBeSavedAsASrtFile()
    {
        $this->assertTrue($this->subCollector->addSubtitleToMovie(__DIR__));
    }

    private function removeDirectoryIncludingContents($dir)
    {
        foreach (glob($dir . '/*') as $file)
        {
            if (is_dir($file))
            {
                $this->removeDirectoryIncludingContents($file);
            }
            else
            {
                unlink($file);
            }
        }
        rmdir($dir);
    }
}
