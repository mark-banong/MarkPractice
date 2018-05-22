<?php
/**
 * @package Sefab Plugin
 */
namespace Inc\Services;

class Html
{
    public function remove_span_tag($titleWithSpan)
    {
        $removedSpanTag = preg_replace('/^(.*?) \* $/', '$1', $titleWithSpan);
        return $removedSpanTag;
    }
}
?>