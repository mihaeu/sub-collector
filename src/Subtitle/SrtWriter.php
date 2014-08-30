<?php

namespace Mihaeu\SubCollector\Subtitle;

class SrtWriter implements ISubtitleWriter
{
    /**
     * Renders captions using specified subtitle writer
     *
     * @param array $caps array of Caption
     * @param string $lf
     * @return string
     */
    public static function render(array $caps, $lf = "\r\n")
    {
        $res = '';
        $seq = 0;

        foreach ($caps as $cap) {
            $seq++;
            $res .=
            $seq.$lf.
            self::renderDuration($cap->startTime).
            ' --> '.
            self::renderDuration($cap->startTime + $cap->duration).$lf.
            implode($lf, $cap->text).$lf.
            $lf;
        }

        return $res;
    }

    /**
     * Renders a second representation as "00:00:06,018"
     *
     * @param int $secs
     */
    public static function renderDuration($secs)
    {
        if (!$secs) {
            return '00:00:00,000';
        }

        $frac = $secs - (int) $secs;

        $secs = intval($secs);

        $m = (int) ($secs / 60);
        $s = $secs % 60;
        $h = (int) ($m / 60);
        $m = $m % 60;

        $s = self::roundExact($s + $frac, 3);

        if ($h < 10) $h = '0'.$h;
        if ($m < 10) $m = '0'.$m;
        if ($s < 10) $s = '0'.$s;

        $s = str_replace('.', ',', $s);

        return $h.':'.$m.':'.$s;
    }

    /**
     * Rounds a number to exactly $precision number of decimals,
     * padding with zeros if nessecary
     *
     * @param float $val
     * @param int $precision
     */
    private static function roundExact($val, $precision)
    {
        $ex = explode('.', round($val, $precision));

        if (empty($ex[1]) || strlen($ex[1]) < $precision) {
            $ex[1] = str_pad(!empty($ex[1]) ? $ex[1] : 0, $precision, '0');
        }

        return implode('.', $ex);
    }

}
