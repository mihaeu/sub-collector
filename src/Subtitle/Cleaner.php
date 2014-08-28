<?php

namespace Mihaeu\SubCollector\Subtitle;

class Cleaner
{
    var $output = null;
    public function __construct(\Symfony\Component\Console\Output\ConsoleOutput $output)
    {
        $this->output = $output;
    }

    public function cleanUpFile($fileName)
    {
        $data = file_get_contents($fileName);

        try {
            $arr = SrtParser::parse($data);
            $arr = self::cleanupCaptions($arr);
            $outData = SrtWriter::render($arr);
            file_put_contents($fileName, $outData);
        } catch (\Exception $e) {
            echo "EXCEPTION: ".$e->getMessage()."\n";
        }
    }

    var $cleanedCaps = array();  ///< cleaned up caps
    var $changes = 0;            ///< number of changes performed

    /**
     * @param $caps array of Subtitle\Caption
     * @return array of cleaned up caps
     */
    private function cleanupCaptions(array $caps)
    {
        $strings = array(
        // eng subs:
        'subtitles:', 'subtitles by', 'captioning by',
        'transcript :', 'transcript:', 'transcript by', 'sync and corrected',
        'sync by n17t01',
        'sync,', 'synchro :', 'synchro:', 'synchronized by', 'synchronization by',
        'resync:', 'resynchro',
        'encoded by',
        'subscene',
        'seriessub',
        'addic7ed', 'addicted.com', 'allsubs.org', 'hdbits.org', 'bierdopje.com',
        'ragbear.com', 'ydy.com', 'yyets.net', 'indivx.net', 'sub-way.fr', 'forom.com',
        'napisy.org', '1000fr.com', 'opensubtitles.org', 'o p e n s u b t i t l e s',
        'sous-titres.eu', '300mbfilms.com',
        'thepiratebay',
        'MKV Player',
        // swe subs:
        'swedish subtitles',
        'undertexter.se','undertexter. se', 'swesub.nu', 'divxsweden.net',
        'undertext av', 'översatt av', 'översättning av', 'rättad av', 'synkad av', 'synkat av',
        'text av', 'text:', 'synk:', 'synkning:', 'transkribering:', 'korrektur:',
        'mediatextgruppen', 'texter på nätet',
        );

        $cleanedCaps = array();
        $changes = 0;
        foreach ($caps as $cap) {
            $skip = false;
            for ($i = 0; $i < count($cap->text); $i++) {
                foreach ($strings as $s) {
                    if (mb_stripos(utf8_encode($cap->text[$i]), $s) !== false) {
                        $s = 'Removed cap '.$cap->seq.": ";

                        foreach ($cap->text as $t) {
                            $s .= '"'.$t."\",\t";
                        }

                        $this->output->writeln($s);

                        $skip = true;
                        $this->changes++;
                        break;
                    }
                }

                if (substr($cap->text[$i], -2) == '?.') {
                    $cap->text[$i] = substr($cap->text[$i], 0, -1);

                    $this->output->writeln('Changed cap '.$cap->seq.': ?. -> ? in "'.$cap->text[$i]."\"");
                    $skip = true;
                    $changes++;
                }

                if ($skip) {
                    break;
                }
            }

            if ($skip) {
                continue;
            }

            $cleanedCaps[] = $cap;
        }

        $this->changes = $changes;
        return $cleanedCaps;
    }
}
