<?php

namespace Mihaeu\Subtitle;

interface ISubtitleWriter
{
    /**
     * @param array $caps array of \Reader\SubtitleCaption
     */
    static function render(array $caps);
}
