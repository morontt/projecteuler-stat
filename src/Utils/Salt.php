<?php

namespace MttProjecteuler\Utils;

class Salt
{
    /**
     * @return string
     *
     * @throws \Exception
     */
    public static function generate(): string
    {
        return substr(base_convert(bin2hex(random_bytes(14)), 16, 36), 0, 20);
    }
}
