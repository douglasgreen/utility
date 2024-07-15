<?php

declare(strict_types=1);

namespace DouglasGreen\Utility\Network;

use DouglasGreen\Utility\Data\ValueException;
use DouglasGreen\Utility\FileSystem\FileException;
use DouglasGreen\Utility\FileSystem\Path;
use Stringable;

class UrlFetcher implements Stringable
{
    protected readonly Path $path;

    protected readonly string $url;

    public function __construct(string $url)
    {
        $filteredUrl = filter_var($url, FILTER_VALIDATE_URL);
        if ($filteredUrl === false) {
            throw new ValueException('Bad URL: ' . $url);
        }

        $decodedUrl = Url::isEncoded($filteredUrl) ? urldecode($filteredUrl) : $filteredUrl;

        $this->url = $decodedUrl;

        // Path can't be injected with DI because $url must be cleaned up before new Path().
        $this->path = new Path(Url::encode($this->url));
    }

    public function __toString(): string
    {
        return $this->url;
    }

    /**
     * Substitute for file_get_contents on a URL.
     */
    public function fetchPage(): ?string
    {
        // Call getPath() so Path can be mocked in unit tests.
        $path = $this->getPath();
        try {
            return $path->loadString();
        } catch (FileException) {
            return null;
        }
    }

    public function getPath(): Path
    {
        return $this->path;
    }
}
