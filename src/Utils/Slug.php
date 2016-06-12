<?php
/**
 * Created by PhpStorm.
 * User: morontt
 * Date: 12.06.16
 * Time: 12:06
 */

namespace MttProjecteuler\Utils;


class Slug
{
    /**
     * @param string $text
     * @return string
     */
    public static function slugify($text)
    {
        $text = transliterator_transliterate('Any-Latin; Latin-ASCII; Lower', $text);
        $text = preg_replace('/[^[:alnum:]]+/', '-', $text);
        $text = trim($text, '-');
        $text = preg_replace('/-+/', '-', $text);

        return $text;
    }
}
