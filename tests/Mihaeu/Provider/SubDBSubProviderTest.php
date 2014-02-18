<?php

class SubDBSubProviderTest extends PHPUnit_Framework_TestCase
{
    /**
     * Hash was taken from http://thesubdb.com/api/ example hash.
     *
     * @see http://thesubdb.com/api/
     */
    public function testDownloadsSubtitleFromSubDBDatabase()
    {
        if ($this->isConnected() === false) {
            $this->markTestSkipped("Provider tests require an internet connection.");
        }

        $provider = new Mihaeu\Provider\SubDBSubProvider();
        $actualSubtitle = $provider->downloadSubtitleByHash('ffd8d4aa68033dc03d1c8ef373b9028c');

        $lf = "\n";
        $expectedSubtitle = "1$lf".
            "00:00:05,000 --> 00:00:15,000$lf".
            "Atention: This is a test subtitle.$lf".
            " $lf".
            "2 $lf".
            "00:00:25,000 --> 00:00:40,000$lf".
            "SubDB - the free subtitle database$lf".
            "http://thesubdb.com$lf";
        $this->assertEquals($expectedSubtitle, $actualSubtitle);
    }

    public function testCreatesCorrectHashForSubDB()
    {
        $provider = new Mihaeu\Provider\SubDBSubProvider();
        $actualHash = $provider->createMovieHashFromMovieFile(
            new Mihaeu\Movie\Movie(__DIR__.'/subDbTestMovie.mp4')
        );
        $expectedHash = 'ffd8d4aa68033dc03d1c8ef373b9028c';
        $this->assertEquals($expectedHash, $actualHash);
    }

    private function isConnected()
    {
        $connection = @fsockopen("google.com", 80, $errno, $errstr, 2);
        if ($connection) {
            $isConnected = true; // action when connected
            fclose($connection);
        } else {
            $isConnected = false; // action in connection failure
        }
        return $isConnected;
    }
}
