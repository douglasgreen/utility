<?php

declare(strict_types=1);

namespace DouglasGreen\Utility\Network;

use DouglasGreen\Utility\Data\ValueException;

class UrlFetcher
{
    protected readonly string $url;

    public function __construct(string $url)
    {
        $filteredUrl = filter_var($url, FILTER_VALIDATE_URL);
        if ($filteredUrl === false) {
            throw new ValueException('Bad URL: ' . $url);
        }

        $this->url = Url::isEncoded($filteredUrl) ? urldecode($filteredUrl) : $filteredUrl;
    }

    /**
     * Fetch a page's content from its URL.
     */
    public function fetchPage(): ?string
    {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $this->url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_CONNECTTIMEOUT => 30,
        ]);

        $response = curl_exec($curl);
        curl_close($curl);

        // PHPStan thinks this function call returns true on success even though the PHP
        // documentation says it shouldn't because CURLOPT_RETURNTRANSFER was set.
        if ($response === false || $response === true) {
            return null;
        }

        return $response;
    }

    public function getUrl(): string
    {
        return $this->url;
    }
}
