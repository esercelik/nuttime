<?php

namespace App\Support;

use Illuminate\Support\HtmlString;

final class AllowedInlineHtml
{
    private const ENCODED_TAG_PATTERN = '/&lt;\s*(\/?)\s*(br|em|strong|b|i)\s*(\/?)\s*&gt;/iu';

    private const TAG_PATTERN = '/<\s*(\/?)\s*(br|em|strong|b|i)\b[^>]*>/iu';

    public function render(?string $value): HtmlString
    {
        $content = $this->decodeAllowedTags($value ?? '');
        $tags = [];

        $content = preg_replace_callback(self::TAG_PATTERN, function (array $matches) use (&$tags): string {
            $tag = strtolower($matches[2]);
            $html = $tag === 'br'
                ? '<br>'
                : ($matches[1] === '/' ? '</'.$tag.'>' : '<'.$tag.'>');
            $placeholder = "\x1A".count($tags)."\x1A";
            $tags[$placeholder] = $html;

            return $placeholder;
        }, $content) ?? $content;

        return new HtmlString(strtr(e(strip_tags($content)), $tags));
    }

    private function decodeAllowedTags(string $value): string
    {
        return preg_replace_callback(self::ENCODED_TAG_PATTERN, function (array $matches): string {
            $tag = strtolower($matches[2]);

            if ($tag === 'br') {
                return '<br>';
            }

            return $matches[1] === '/' ? '</'.$tag.'>' : '<'.$tag.'>';
        }, $value) ?? $value;
    }
}
