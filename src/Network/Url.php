<?php

declare(strict_types=1);

namespace DouglasGreen\Utility\Network;

class Url
{
    /**
     * Check if a URL has been encoded with urlencode() or rawurlencode().
     */
    public static function isEncoded(string $url): bool
    {
        return urldecode($url) !== $url;
    }

    /**
     * Encode a URL with rawurlencode without checking if it was encoded first.
     */
    public static function encode(string $url): string
    {
        $parts = parse_url($url);
        $encodedUrl = '';

        if (isset($parts['scheme'])) {
            $encodedUrl .= $parts['scheme'] . '://';
        }

        if (isset($parts['user'])) {
            $encodedUrl .= rawurlencode($parts['user']);
            if (isset($parts['pass'])) {
                $encodedUrl .= ':' . rawurlencode($parts['pass']);
            }

            $encodedUrl .= '@';
        }

        if (isset($parts['host'])) {
            $encodedUrl .= $parts['host'];
        }

        if (isset($parts['path'])) {
            $encodedUrl .= '/' . implode(
                '/',
                array_map(rawurlencode(...), explode('/', trim($parts['path'], '/')))
            );
        }

        if (isset($parts['query'])) {
            parse_str($parts['query'], $query_params);
            $safe_query = http_build_query($query_params);
            $encodedUrl .= '?' . $safe_query;
        }

        if (isset($parts['fragment'])) {
            $encodedUrl .= '#' . rawurlencode($parts['fragment']);
        }

        return $encodedUrl;
    }
}
