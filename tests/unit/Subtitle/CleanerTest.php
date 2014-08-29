<?php

namespace Mihaeu\SubCollector\Tests\Subtitle;

class CleanerTest extends \PHPUnit_Framework_TestCase
{
    function test1()
    {
        $reader = new \Mihaeu\SubCollector\Subtitle\SrtParser();

        $text =
            "0\n". // wrong start index
            "00:00:04,630 --> 00:00:06,018\n".
            "<i>Go ninja!</i>\n";

        $cleaner = new \Mihaeu\SubCollector\Subtitle\Cleaner();
        $cleanCaps = $cleaner->cleanupCaptions(
            $reader->parse($text)
        );

        $this->assertEquals(0, $cleaner->changes);

        $this->assertEquals(
            "1\r\n". // fixed start index & line feeds
            "00:00:04,630 --> 00:00:06,018\r\n".
            "<i>Go ninja!</i>\r\n".
            "\r\n",
            \Mihaeu\SubCollector\Subtitle\SrtWriter::render($cleanCaps)
        );
    }
}
