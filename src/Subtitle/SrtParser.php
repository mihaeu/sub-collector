<?php

namespace Mihaeu\SubCollector\Subtitle;

/**
 * SubRip subtitle reader (.srt)
 */
class SrtParser implements ISubtitleParser
{
    private static function convertToUtf8($data)
    {
        if (substr($data, 0, 3) == "\xEF\xBB\xBF") {
            // UTF-8 BOM marker
            return substr($data, 3);
        }

        if (substr($data, 0, 2) == "\xFF\xFE") {
            // UTF-16-LE BOM marker
            $data = mb_convert_encoding(substr($data, 2), 'UTF-8', 'UTF-16LE');
        }

        $enc = mb_detect_encoding($data);
        if (in_array($enc, array('ASCII', 'UTF-8'))) {
            return $data;
        }

        return $data;
    }

    private static function convertToUnixLinefeeds($data)
    {
        return str_replace("\r\n", "\n", trim($data));
    }

    /**
     * Check if input string is a time string, such as HH:MM,
     * HH:MM:SS, HH:MM:SS.mmm or HH:MM:SS,mmm
     *
     * @return bool
     */
    private static function isSrtTimeFormat($s)
    {
        $regexp =
        '/^([0-9]+)'.
            ':[0-9]+'.
            '(:[0-9]+'.
                '([\.\,]\d{1,3})'.
            '?)'.
        '?$/';
        preg_match_all($regexp, $s, $matches);

        if ($matches && $matches[0] && $matches[0][0] == $s) {
            return true;
        }

        return false;
    }

    /**
     * Translates a time string to seconds
     *
     * @param string $s "18:40:22", "18:40:22.11" or "18:40:22,11"
     * @return duration in seconds
     */
    private static function srtTimeFormatInSeconds($s)
    {
        if (!self::isSrtTimeFormat($s)) {
            throw new \InvalidArgumentException('not a time string: '.$s);
        }

        $x = explode(':', $s);
        if (count($x) != 3) {
            throw new \InvalidArgumentException('bad format: '.$s);
        }

        $x[2] = str_replace(',', '.', $x[2]);

        return ($x[0] * 3600) + ($x[1] * 60) + $x[2];
    }

    /**
     * @param string $data
     * @return array of \Reader\SubtitleCaption
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    public static function parse($data)
    {
        $data = self::convertToUtf8($data);

        $data = self::convertToUnixLinefeeds($data);

        $rows = explode("\n", $data);

        $caps = array();

        $seqCounter = 0;

        for ($i = 0; $i <= count($rows); $i++) {
            $rows[$i] = trim($rows[$i]);

            if ($rows[$i] === '') {
                continue;
            }

            if (!is_numeric($rows[$i])) {
                throw new \Exception(
                    'expected sequence number, found odd data at line '.($i+1).
                    ': "'.$rows[$i]."\"\n".$rows[$i+1]
                );
            }

            $seqCounter++;

            $cap = new Caption();
            $cap->seq = $seqCounter;

            // 00:26:36,595 --> 00:26:40,656
            $aa = explode(' --> ', trim($rows[$i+1]));

            $cap->startTime = self::srtTimeFormatInSeconds($aa[0]);
            $cap->duration  = self::srtTimeFormatInSeconds($aa[1]) - $cap->startTime;

            // find multi-line sub, allow all text until new numeric is found (next chunk)
            for ($j=2; $j <= 5; $j++) {
                if (!isset($rows[$i+$j])) {
                    break;
                }

                $rows[$i+$j] = trim($rows[$i+$j]);
                if (!$rows[$i+$j]) {
                    break;
                }

                if (is_numeric($rows[$i+$j]) && $j >= 3 && ($rows[$i+$j] <= 10000)) {
                    throw new \Exception("XXX: breaking at row ".($i+$j)." on data ". ($rows[$i+$j]));
                    $i--;
                    break;
                }

                // allow first line to be empty (found in some crappy files)
                if ($j>2 && !$rows[$i+$j]) {
                    break;
                }

                if ($rows[$i+$j]) {
                    $cap->text[] = $rows[$i+$j];
                }
            }

            $i += $j;

            // make 0-duration text show for 1 second
            if ($cap->duration <= 0 && !empty($cap->text)) {
                $cap->duration = 1;
            }

            // exclude caps without text
            if (empty($cap->text)) {
                continue;
            }

            $caps[] = $cap;
        }

        return $caps;
    }
}
