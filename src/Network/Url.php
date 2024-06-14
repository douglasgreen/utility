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

    public function __construct(
        protected string $url
    ) {
        if (self::isEncoded($this->url)) {
            $this->url = urldecode($this->url);
        }
    }

    /**
     * Substitute for file_get_contents on a URL.
     *
     * Automatically encodes the URL if not already encoded.
     */
    public function fetchPage(): ?string
    {
        $result = file_get_contents(urlencode($this->url));

        return $result === false ? null : $result;
    }
}
