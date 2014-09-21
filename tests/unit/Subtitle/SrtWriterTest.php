<?php

namespace Mihaeu\SubCollector\Tests\Subtitle;

use Mihaeu\SubCollector\Subtitle\Caption;
use Mihaeu\SubCollector\Subtitle\SrtWriter;

class SrtWriterTest extends \PHPUnit_Framework_TestCase
{
    public function testWritesCleanDataToSrt()
    {
        $cap = new Caption();
        $cap->seq = 1;
        $cap->startTime = 6.053;
        $cap->duration = 2.085;
        $cap->text = array('<i>[Randy] I was chosen', 'to protect my school', 'from all evil</i>');

        $text =
            "1\r\n".
            "00:00:06,053 --> 00:00:08,138\r\n".
            "<i>[Randy] I was chosen\r\n".
            "to protect my school\r\n".
            "from all evil</i>\r\n".
            "\r\n";

        $this->assertEquals(
            $text,
            SrtWriter::render(array($cap))
        );
    }
}
