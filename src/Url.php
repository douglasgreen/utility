<?php

declare(strict_types=1);

namespace DouglasGreen\Utility;

use DouglasGreen\Utility\Exceptions\Data\ValueException;
use DouglasGreen\Utility\Exceptions\Process\ParseException;

/**
 * A wrapper class for the PHP parse_url function that parses a URL into its components.
 * It provides methods to get and set each component of the URL, reassemble the URL, and
 * validate the constructed URL.
 *
 * Usage example:
 *
 * try {
 *     $urlParser = new Url('http://username:password@hostname:9090/path?arg=value#anchor');
 *
 *     // Getting URL components
 *     $scheme = $urlParser->getScheme(); // 'http'
 *     $host = $urlParser->getHost(); // 'hostname'
 *
 *     // Setting URL components
 *     $urlParser->setScheme('https');
 *     $urlParser->setHost('example.com');
 *     $urlParser->setParam('example', 'yes');
 *
 *     // Reassembling and validating the URL
 *     $newUrl = $urlParser->getURL(); // 'https://username:password@example.com:9090/path?arg=value&example=yes#anchor'
 *     echo $newUrl;
 * } catch (ParseException $e) {
 *     echo 'Error: ' . $e->getMessage();
 * }
 *
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class Url implements \Stringable
{
    /**
     * @var list<string>
     */
    public const array ACCEPTED_SCHEMES = [
        'http',
        'https',
        'ftp',
        'ftps',
        'mailto',
        'file',
        'data',
        'tel',
        'ws',
        'wss',
    ];

    /**
     * @var ?array<string, mixed>
     */
    protected ?array $params = null;

    protected ?string $fragment;

    protected ?string $host;

    protected ?string $pass;

    protected ?string $path;

    protected ?string $scheme;

    protected ?string $user;

    protected ?int $port;

    /**
     * @throws ParseException
     */
    public function __construct(string $url)
    {
        $parsedUrl = parse_url($url);
        if ($parsedUrl === false) {
            throw new ParseException(sprintf(
                'Failed to parse URL: "%s"',
                $url
            ));
        }

        $this->setScheme($parsedUrl['scheme'] ?? null);
        $this->setHost($parsedUrl['host'] ?? null);
        $this->setPort($parsedUrl['port'] ?? null);
        $this->setUser($parsedUrl['user'] ?? null);
        $this->setPass($parsedUrl['pass'] ?? null);
        $this->setPath($parsedUrl['path'] ?? null);
        if (isset($parsedUrl['query'])) {
            $this->setQuery($parsedUrl['query']);
        }

        $this->setFragment($parsedUrl['fragment'] ?? null);
    }

    public function __toString(): string
    {
        return $this->getUrl();
    }

    public function getFragment(): ?string
    {
        return $this->fragment;
    }

    public function getHost(): ?string
    {
        return $this->host;
    }

    public function getParam(string $key): mixed
    {
        return $this->params[$key] ?? null;
    }

    public function getPass(): ?string
    {
        return $this->pass;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function getPort(): ?int
    {
        return $this->port;
    }

    public function getQuery(): ?string
    {
        if ($this->params !== null) {
            return http_build_query($this->params);
        }

        return null;
    }

    public function getScheme(): ?string
    {
        return $this->scheme;
    }

    public function getUrl(): string
    {
        $url = '';
        if ($this->scheme !== null) {
            $url .= $this->scheme . '://';
        }

        if ($this->user !== null) {
            $url .= $this->user;
            if ($this->pass !== null) {
                $url .= ':' . $this->pass;
            }

            $url .= '@';
        }

        if ($this->host !== null) {
            $url .= $this->host;
        }

        if ($this->port !== null) {
            $url .= ':' . $this->port;
        }

        if ($this->path !== null) {
            $url .= $this->path;
        }

        if ($this->params !== null) {
            $url .= '?' . $this->getQuery();
        }

        if ($this->fragment !== null) {
            $url .= '#' . $this->fragment;
        }

        return $url;
    }

    public function getUser(): ?string
    {
        return $this->user;
    }

    public function setFragment(?string $fragment): void
    {
        $this->fragment = $this->strip($fragment);
    }

    /**
     * @throws ValueException
     */
    public function setHost(?string $host): void
    {
        $host = $this->strip($host);
        if (
            $host !== null &&
            ! filter_var($host, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)
        ) {
            throw new ValueException(sprintf('Invalid host: "%s"', $host));
        }

        $this->host = $host;
    }

    public function setParam(string $key, mixed $value): void
    {
        if (! is_string($key)) {
            return;
        }

        $key = $this->strip($key);
        if ($key === '') {
            return;
        }

        if (is_string($value)) {
            $value = $this->strip($value);
            if ($value === '') {
                return;
            }
        } elseif (is_array($value)) {
            if ($value === []) {
                return;
            }
        } else {
            return;
        }

        if ($this->params === null) {
            $this->params = [];
        }

        $this->params[$key] = $value;
    }

    public function setPass(?string $pass): void
    {
        $this->pass = $this->strip($pass);
    }

    public function setPath(?string $path): void
    {
        $this->path = $this->strip($path);
    }

    /**
     * @throws ValueException
     */
    public function setPort(?int $port): void
    {
        if ($port !== null && ($port < 1 || $port > 65535)) {
            throw new ValueException(sprintf('Invalid port: %d', $port));
        }

        $this->port = $port;
    }

    public function setQuery(?string $query): void
    {
        $query = $this->strip($query);
        if ($query === null) {
            return;
        }

        parse_str($query, $params);
        $this->params = [];
        foreach ($params as $key => $value) {
            if (is_int($key)) {
                continue;
            }

            $this->setParam($key, $value);
        }
    }

    /**
     * @throws ValueException
     */
    public function setScheme(?string $scheme): void
    {
        if ($scheme !== null && ! in_array(
            $scheme,
            self::ACCEPTED_SCHEMES,
            true
        )) {
            throw new ValueException(sprintf('Invalid scheme: "%s"', $scheme));
        }

        $this->scheme = $this->strip($scheme);
    }

    public function setUser(?string $user): void
    {
        $this->user = $this->strip($user);
    }

    /**
     * Strip extra spaces and return null if empty
     */
    protected function strip(?string $input): ?string
    {
        if ($input === null) {
            return null;
        }

        $input = trim($input);
        if ($input === '') {
            return null;
        }

        return $input;
    }
}
