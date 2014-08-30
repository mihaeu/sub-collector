<?php

namespace Mihaeu\SubCollector\Tests\Subtitle;

use Mihaeu\SubCollector\Subtitle\Cleaner;
use Mihaeu\SubCollector\Subtitle\SrtParser;
use Mihaeu\SubCollector\Subtitle\SrtWriter;

class CleanerTest extends \PHPUnit_Framework_TestCase
{
    public function testCleansUpBadStartIndexAndLineFeeds()
    {
        $reader = new SrtParser();

        $text =
            "0\n". // wrong start index
            "00:00:04,630 --> 00:00:06,018\n".
            "<i>Go ninja!</i>\n";

        $cleaner = new Cleaner();
        $cleanCaps = $cleaner->cleanupCaptions(
            $reader->parse($text)
        );

        $this->assertEquals(0, $cleaner->changes);

        $this->assertEquals(
            "1\r\n". // fixed start index & line feeds
            "00:00:04,630 --> 00:00:06,018\r\n".
            "<i>Go ninja!</i>\r\n".
            "\r\n",
            SrtWriter::render($cleanCaps)
        );
    }
}
