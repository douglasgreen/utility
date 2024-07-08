<?php

declare(strict_types=1);

namespace DouglasGreen\Utility\Network;

class Url
{
    /**
     * Check if a URL has already been encoded.
     */
    public static function isEncoded(string $url): bool
    {
        return urldecode($url) !== $url;
    }
}
