<?php

declare(strict_types=1);

namespace DouglasGreen\Utility\Network;

use DouglasGreen\Utility\Data\ArrayUtils;
use DouglasGreen\Utility\Data\ValueException;
use DouglasGreen\Utility\Process\ParseException;
use Stringable;

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
 */
class Url implements Stringable
{
    /**
     * @var list<string>
     */
    public const ACCEPTED_SCHEMES = [
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
     * @var array<string|int, mixed>
     */
    protected array $params = [];

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
        if ($this->isEncoded($url)) {
            $url = urldecode($url);
        }

        $parsedUrl = parse_url($url);
        if ($parsedUrl === false) {
            throw new ParseException(sprintf('Failed to parse URL: "%s"', $url));
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

    public function deleteParam(string $key): self
    {
        unset($this->params[$key]);

        return $this;
    }

    /**
     * Substitute for file_get_contents on a URL.
     *
     * Automatically encodes the URL if not already encoded.
     */
    public function fetchPage(): ?string
    {
        $url = $this->getUrl();

        $result = file_get_contents(urlencode($url));

        return $result === false ? null : $result;
    }

    public function getFragment(): ?string
    {
        return $this->fragment;
    }

    public function getHost(): ?string
    {
        return $this->host;
    }

    public function getParam(string $key): string|float|int|null
    {
        if (! isset($this->params[$key])) {
            return null;
        }

        if (is_array($this->params[$key])) {
            return null;
        }

        return $this->params[$key];
    }

    /**
     * @return array<string|int, mixed>|null
     */
    public function getParamArray(string $key): ?array
    {
        if (! isset($this->params[$key])) {
            return null;
        }

        if (! is_array($this->params[$key])) {
            return null;
        }

        return $this->params[$key];
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
        if ($this->params !== []) {
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

        if ($this->params !== []) {
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

    public function hasParam(string $key): bool
    {
        return isset($this->params[$key]);
    }

    public function isEqual(self $otherUrl): bool
    {
        if (! ArrayUtils::equal($this->params, $otherUrl->params)) {
            return false;
        }

        if ($this->fragment !== $otherUrl->fragment) {
            return false;
        }

        if ($this->host !== $otherUrl->host) {
            return false;
        }

        if ($this->pass !== $otherUrl->pass) {
            return false;
        }

        if ($this->path !== $otherUrl->path) {
            return false;
        }

        if ($this->scheme !== $otherUrl->scheme) {
            return false;
        }

        if ($this->user !== $otherUrl->user) {
            return false;
        }

        return $this->port === $otherUrl->port;
    }

    public function setFragment(?string $fragment): self
    {
        $this->fragment = $this->strip($fragment);

        return $this;
    }

    /**
     * @throws ValueException
     */
    public function setHost(?string $host): self
    {
        $host = $this->strip($host);
        if ($host !== null && ! filter_var($host, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)) {
            throw new ValueException(sprintf('Invalid host: "%s"', $host));
        }

        $this->host = $host;

        return $this;
    }

    public function setParam(string $key, string|float|int $value): self
    {
        $key = $this->strip($key);
        if ($key === '') {
            return $this;
        }

        if (is_string($value)) {
            $value = $this->strip($value);
            if ($value === '') {
                return $this;
            }
        }

        $this->params[$key] = $value;

        return $this;
    }

    /**
     * @param array<string|int, mixed> $value
     */
    public function setParamArray(string $key, array $value): self
    {
        $key = $this->strip($key);
        if ($key === '') {
            return $this;
        }

        if ($value === []) {
            return $this;
        }

        $this->params[$key] = $value;

        return $this;
    }

    public function setPass(?string $pass): self
    {
        $this->pass = $this->strip($pass);

        return $this;
    }

    public function setPath(?string $path): self
    {
        $this->path = $this->strip($path);

        return $this;
    }

    /**
     * @throws ValueException
     */
    public function setPort(?int $port): self
    {
        if ($port !== null && ($port < 1 || $port > 65535)) {
            throw new ValueException(sprintf('Invalid port: %d', $port));
        }

        $this->port = $port;

        return $this;
    }

    public function setQuery(?string $query): self
    {
        $query = $this->strip($query);
        if ($query === null) {
            return $this;
        }

        parse_str($query, $params);
        $this->params = [];
        foreach ($params as $key => $value) {
            if (is_int($key)) {
                continue;
            }

            if (is_array($value)) {
                $this->setParamArray($key, $value);
            } else {
                $this->setParam($key, $value);
            }
        }

        return $this;
    }

    /**
     * @throws ValueException
     */
    public function setScheme(?string $scheme): self
    {
        if ($scheme !== null && ! in_array($scheme, self::ACCEPTED_SCHEMES, true)) {
            throw new ValueException(sprintf('Invalid scheme: "%s"', $scheme));
        }

        $this->scheme = $this->strip($scheme);

        return $this;
    }

    public function setUser(?string $user): self
    {
        $this->user = $this->strip($user);

        return $this;
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

    /**
     * Check if a URL has already been encoded.
     */
    protected function isEncoded(string $url): bool
    {
        return urldecode($url) !== $url;
    }
}