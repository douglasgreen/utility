<?php

declare(strict_types=1);

namespace DouglasGreen\Utility\Regex;

use DouglasGreen\Utility\Data\TypeException;

/**
 * Regex utility class to throw exceptions when basic operations fail.
 */
class Matcher
{
    public const NO_EMPTY = 1;

    public const DELIM_CAPTURE = 2;

    protected int $count;

    /**
     * @param list<string>|string $pattern
     */
    public function __construct(
        protected array|string $pattern
    ) {}

    /**
     * Substitute for preg_filter.
     *
     * The function name indicates that preg_filter is identical to preg_replace
     * except it only returns the subjects where there was a match.
     *
     * Takes a string as $subject so it returns a string.
     *
     * @param list<string>|string $replacement
     * @throws RegexException
     */
    public function filteredReplace(
        array|string $replacement,
        string $subject,
        int $limit = -1,
    ): string {
        $result = preg_filter($this->pattern, $replacement, $subject, $limit);

        if ($result === null) {
            throw new RegexException('Regex failed: ' . $this->getPatternDesc());
        }

        return $result;
    }

    /**
     * Substitute for preg_filter.
     *
     * The function name indicates that preg_filter is identical to preg_replace
     * except it only returns the subjects where there was a match.
     *
     * Takes an array as $subject so it returns a list<string>.
     *
     * @param list<string>|string $replacement
     * @param list<string> $subject
     * @return list<string>
     * @throws RegexException
     */
    public function filteredReplaceList(
        array|string $replacement,
        array $subject,
        int $limit = -1,
    ): array {
        // preg_filter doesn't distinguish between no matches and error when using
        // array as subject, so I use a second preg_filter call to look for errors.
        if (preg_filter($this->pattern, '', '') === null) {
            throw new RegexException('Regex failed: ' . $this->getPatternDesc());
        }

        return preg_filter($this->pattern, $replacement, $subject, $limit, $this->count);
    }

    public function getCount(): int
    {
        return $this->count;
    }

    public function hasMatch(): bool
    {
        return $this->count > 0;
    }

    /**
     * Do a preg_match and store the results.
     *
     * @return array<string|int, string>
     * @throws RegexException
     * @throws TypeException
     */
    public function match(string $subject, int $offset = 0): array
    {
        if (! is_string($this->pattern)) {
            throw new TypeException('String pattern expected');
        }

        $result = preg_match($this->pattern, $subject, $match, 0, $offset);
        if ($result === false) {
            throw new RegexException('Regex failed: ' . $this->pattern);
        }

        $this->count = $result;

        return $match;
    }

    /**
     * Do a preg_match_all and store the results.
     *
     * @return array<string|int, array<int, string>>
     * @throws RegexException
     * @throws TypeException
     */
    public function matchAll(string $subject, int $offset = 0): array
    {
        if (! is_string($this->pattern)) {
            throw new TypeException('String pattern expected');
        }

        $result = preg_match_all($this->pattern, $subject, $matches, 0, $offset);

        if ($result === false) {
            throw new RegexException('Regex failed: ' . $this->pattern);
        }

        $this->count = $result;

        return $matches;
    }

    /**
     * Do a preg_match_all with offset capture and store the results.
     *
     * @return array<string|int, array<int, array<int, string|int>>>
     * @throws RegexException
     * @throws TypeException
     */
    public function matchAllOffset(string $subject, int $offset = 0): array
    {
        if (! is_string($this->pattern)) {
            throw new TypeException('String pattern expected');
        }

        $result = preg_match_all($this->pattern, $subject, $matches, PREG_OFFSET_CAPTURE, $offset);

        if ($result === false) {
            throw new RegexException('Regex failed: ' . $this->pattern);
        }

        $this->count = $result;

        return $matches;
    }

    /**
     * Do a preg_match with offset capture and store the results.
     *
     * @return array<string|int, array<int, string|int>>
     * @throws RegexException
     * @throws TypeException
     */
    public function matchOffset(string $subject, int $offset = 0): array
    {
        if (! is_string($this->pattern)) {
            throw new TypeException('String pattern expected');
        }

        $result = preg_match($this->pattern, $subject, $match, PREG_OFFSET_CAPTURE, $offset);
        if ($result === false) {
            throw new RegexException('Regex failed: ' . $this->pattern);
        }

        $this->count = $result;

        return $match;
    }

    /**
     * Substitute for preg_replace.
     *
     * Takes a string as $subject so it returns a string.
     *
     * @param list<string>|string $replacement
     * @throws RegexException
     */
    public function replace(array|string $replacement, string $subject, int $limit = -1): string
    {
        $result = preg_replace($this->pattern, $replacement, $subject, $limit);

        if ($result === null) {
            throw new RegexException('Regex failed: ' . $this->getPatternDesc());
        }

        return $result;
    }

    /**
     * Substitute for preg_replace_callback.
     *
     * Takes a string as $subject so it returns a string.
     *
     * @throws RegexException
     */
    public function replaceCall(callable $callback, string $subject, int $limit = -1): string
    {
        $result = preg_replace_callback($this->pattern, $callback, $subject, $limit);

        if ($result === null) {
            throw new RegexException('Regex failed: ' . $this->getPatternDesc());
        }

        return $result;
    }

    /**
     * Substitute for preg_replace_callback.
     *
     * Takes an array as $subject so it returns a list<string>.
     *
     * @param list<string> $subject
     * @return list<string>
     * @throws RegexException
     */
    public function replaceCallList(callable $callback, array $subject, int $limit = -1): array
    {
        $result = preg_replace_callback($this->pattern, $callback, $subject, $limit, $this->count);

        if ($result === null) {
            throw new RegexException('Regex failed: ' . $this->getPatternDesc());
        }

        return $result;
    }

    /**
     * Substitute for preg_replace.
     *
     * Takes an array as $subject so it returns a list<string>.
     *
     * @param list<string>|string $replacement
     * @param list<string> $subject
     * @return list<string>
     * @throws RegexException
     */
    public function replaceList(
        array|string $replacement,
        array $subject,
        int $limit = -1,
    ): array {
        $result = preg_replace($this->pattern, $replacement, $subject, $limit, $this->count);

        if ($result === null) {
            throw new RegexException('Regex failed: ' . $this->getPatternDesc());
        }

        return $result;
    }

    /**
     * Substitute for preg_grep.
     *
     * @param list<string> $array
     * @return list<string>
     * @throws RegexException
     * @throws TypeException
     */
    public function searchList(array $array): array
    {
        if (! is_string($this->pattern)) {
            throw new TypeException('String pattern expected');
        }

        $result = preg_grep($this->pattern, $array);
        if ($result === false) {
            throw new RegexException('Regex failed: ' . $this->pattern);
        }

        $this->count = count($result);

        return $result;
    }

    /**
     * Substitute for preg_grep that inverts the match.
     *
     * @param list<string> $array
     * @return list<string>
     * @throws RegexException
     * @throws TypeException
     */
    public function searchListInverted(array $array): array
    {
        if (! is_string($this->pattern)) {
            throw new TypeException('String pattern expected');
        }

        $result = preg_grep($this->pattern, $array, PREG_GREP_INVERT);
        if ($result === false) {
            throw new RegexException('Regex failed: ' . $this->pattern);
        }

        $this->count = count($result);

        return $result;
    }

    /**
     * Substitute for preg_split.
     *
     * @return list<string>
     * @throws RegexException
     * @throws TypeException
     */
    public function split(string $subject, int $limit = -1, int $flags = 0): array
    {
        if (! is_string($this->pattern)) {
            throw new TypeException('String pattern expected');
        }

        $useFlags = 0;
        if ((bool) ($flags & self::NO_EMPTY)) {
            $useFlags |= PREG_SPLIT_NO_EMPTY;
        } elseif ((bool) ($flags & self::DELIM_CAPTURE)) {
            $useFlags |= PREG_SPLIT_DELIM_CAPTURE;
        }

        $result = preg_split($this->pattern, $subject, $limit, $useFlags);
        if ($result === false) {
            throw new RegexException('Regex failed: ' . $this->pattern);
        }

        $this->count = count($result);

        return $result;
    }

    /**
     * @throws RegexException
     */
    protected function getPatternDesc(): string
    {
        return is_array($this->pattern) ? implode(', ', $this->pattern) : $this->pattern;
    }
}
