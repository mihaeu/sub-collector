<?php

namespace Mihaeu\SubCollector\Subtitle;

interface ISubtitleWriter
{
    /**
     * @param array $caps array of \Reader\SubtitleCaption
     */
    static function render(array $caps);
}
