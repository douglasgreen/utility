<?php

declare(strict_types=1);

namespace DouglasGreen\Utility\Network;

use DouglasGreen\Utility\Data\ArrayUtil;
use DouglasGreen\Utility\Data\ValueException;
use DouglasGreen\Utility\Logic\ParseException;
use Stringable;

/**
 * A wrapper class for the PHP parse_url function that parses a URL into its components.
 * It provides methods to get and set each component of the URL, reassemble the URL, and
 * validate the constructed URL.
 *
 * Usage example:
 *
 * try {
 *     $urlBuilder = new UrlBuilder('http://username:password@hostname:9090/path?arg=value#anchor');
 *
 *     // Getting URL components
 *     $scheme = $urlBuilder->getScheme(); // 'http'
 *     $host = $urlBuilder->getHost(); // 'hostname'
 *
 *     // Setting URL components
 *     $urlBuilder->setScheme('https');
 *     $urlBuilder->setHost('example.com');
 *     $urlBuilder->setParam('example', 'yes');
 *
 *     // Reassembling and validating the URL
 *     $newUrl = $urlBuilder->getURL(); // 'https://username:password@example.com:9090/path?arg=value&example=yes#anchor'
 *     echo $newUrl;
 * } catch (ParseException $e) {
 *     echo 'Error: ' . $e->getMessage();
 * }
 */
class UrlBuilder implements Stringable
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

    protected ?string $fragment = null;

    protected ?string $host = null;

    protected ?string $pass = null;

    protected ?string $path = null;

    protected ?string $scheme = null;

    protected ?string $user = null;

    protected ?int $port = null;

    /**
     * Can pass URL to constructor or start with empty URL.
     */
    public function __construct(string $url = null)
    {
        if ($url !== null) {
            $this->parse($url);
        }
    }

    public function __toString(): string
    {
        return $this->build();
    }

    public function build(): string
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

    public function deleteParam(string $key): self
    {
        unset($this->params[$key]);

        return $this;
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
        if (! ArrayUtil::equal($this->params, $otherUrl->params)) {
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

    /**
     * @throws ParseException
     */
    public function parse(string $url): self
    {
        if (Url::isEncoded($url)) {
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
        $this->setFragment($parsedUrl['fragment'] ?? null);

        if (isset($parsedUrl['query'])) {
            $this->setQuery($parsedUrl['query']);
        } else {
            $this->params = [];
        }

        return $this;
    }

    public function setFragment(?string $fragment): self
    {
        $this->fragment = static::strip($fragment);

        return $this;
    }

    /**
     * @throws ValueException
     */
    public function setHost(?string $host): self
    {
        $host = static::strip($host);
        if ($host !== null && ! filter_var($host, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)) {
            throw new ValueException(sprintf('Invalid host: "%s"', $host));
        }

        $this->host = $host;

        return $this;
    }

    public function setParam(string $key, string|float|int $value): self
    {
        $key = static::strip($key);
        if ($key === '') {
            return $this;
        }

        if (is_string($value)) {
            $value = static::strip($value);
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
        $key = static::strip($key);
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
        $this->pass = static::strip($pass);

        return $this;
    }

    public function setPath(?string $path): self
    {
        $this->path = static::strip($path);

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
        $query = static::strip($query);
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

        $this->scheme = static::strip($scheme);

        return $this;
    }

    public function setUser(?string $user): self
    {
        $this->user = static::strip($user);

        return $this;
    }

    /**
     * Strip extra spaces and return null if empty
     */
    protected static function strip(?string $input): ?string
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
