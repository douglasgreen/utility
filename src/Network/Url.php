<?php

declare(strict_types=1);

namespace DouglasGreen\Utility\Network;

use Stringable;

class Url implements Stringable
{
    protected readonly string $url;

    /**
     * Check if a URL has already been encoded.
     */
    public static function isEncoded(string $url): bool
    {
        return urldecode($url) !== $url;
    }

    public function __construct(string $url)
    {
        if (self::isEncoded($url)) {
            $url = urldecode($url);
        }

        $this->url = $url;
    }

    public function __toString(): string
    {
        return $this->url;
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
