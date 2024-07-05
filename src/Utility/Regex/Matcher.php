<?php

declare(strict_types=1);

namespace DouglasGreen\Utility\Regex;

use DouglasGreen\Utility\Data\TypeException;

/**
 * Regex utility class to throw exceptions when basic operations fail.
 */
class Matcher extends AbstractMatcher
{
    /**
     * @param list<string>|string $pattern
     */
    public function __construct(
        protected readonly array|string $pattern
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
        $result = preg_filter($this->pattern, $replacement, $subject, $limit, $this->count);

        if ($result === null) {
            throw new RegexException($this->getErrorMessage());
        }

        return $result;
    }

    /**
     * Substitute for preg_filter.
     *
     * The function name indicates that preg_filter is identical to preg_replace
     * except it only returns the subjects where there was a match.
     *
     * Takes a list<string> as $subject so it returns a list<string>.
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
        if (preg_filter($this->pattern, $replacement, $subject[0]) === null) {
            throw new RegexException($this->getErrorMessage());
        }

        return preg_filter($this->pattern, $replacement, $subject, $limit, $this->count);
    }

    /**
     * @throws RegexException
     * @throws TypeException
     */
    public function hasMatch(string $subject, int $offset = 0): bool
    {
        if (! is_string($this->pattern)) {
            throw new TypeException('String pattern expected');
        }

        $result = preg_match($this->pattern, $subject, $match, 0, $offset);
        if ($result === false) {
            throw new RegexException($this->getErrorMessage());
        }

        return $match !== [];
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
            throw new RegexException($this->getErrorMessage());
        }

        $this->count = $result;

        return $match;
    }

    /**
     * Do a preg_match_all and store the results.
     *
     * Returns [] as a convenience if there are no matches.
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
            throw new RegexException($this->getErrorMessage());
        }

        $this->count = $result;
        if ($this->count > 0) {
            return $matches;
        }

        return [];
    }

    /**
     * Do a preg_match_all with PREG_SET_ORDER and store the results.
     *
     * @return array<int, array<string|int, string>>
     * @throws RegexException
     * @throws TypeException
     */
    public function matchAllSetOrder(string $subject, int $offset = 0): array
    {
        if (! is_string($this->pattern)) {
            throw new TypeException('String pattern expected');
        }

        $result = preg_match_all($this->pattern, $subject, $matches, PREG_SET_ORDER, $offset);

        if ($result === false) {
            throw new RegexException($this->getErrorMessage());
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
    public function matchAllWithOffsets(string $subject, int $offset = 0): array
    {
        if (! is_string($this->pattern)) {
            throw new TypeException('String pattern expected');
        }

        $result = preg_match_all($this->pattern, $subject, $matches, PREG_OFFSET_CAPTURE, $offset);

        if ($result === false) {
            throw new RegexException($this->getErrorMessage());
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
    public function matchWithOffsets(string $subject, int $offset = 0): array
    {
        if (! is_string($this->pattern)) {
            throw new TypeException('String pattern expected');
        }

        $result = preg_match($this->pattern, $subject, $match, PREG_OFFSET_CAPTURE, $offset);
        if ($result === false) {
            throw new RegexException($this->getErrorMessage());
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
        $result = preg_replace($this->pattern, $replacement, $subject, $limit, $this->count);

        if ($result === null) {
            throw new RegexException($this->getErrorMessage());
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
        $result = preg_replace_callback($this->pattern, $callback, $subject, $limit, $this->count);

        if ($result === null) {
            throw new RegexException($this->getErrorMessage());
        }

        return $result;
    }

    /**
     * Substitute for preg_replace_callback.
     *
     * Takes a list<string> as $subject so it returns a list<string>.
     *
     * @param list<string> $subject
     * @return list<string>
     * @throws RegexException
     */
    public function replaceCallList(callable $callback, array $subject, int $limit = -1): array
    {
        $result = preg_replace_callback($this->pattern, $callback, $subject, $limit, $this->count);

        if ($result === null) {
            throw new RegexException($this->getErrorMessage());
        }

        return $result;
    }

    /**
     * Substitute for preg_replace.
     *
     * Takes a list<string> as $subject so it returns a list<string>.
     *
     * @param list<string>|string $replacement
     * @param list<string> $subject
     * @return list<string>
     * @throws RegexException
     */
    public function replaceList(array|string $replacement, array $subject, int $limit = -1): array
    {
        $result = preg_replace($this->pattern, $replacement, $subject, $limit, $this->count);

        if ($result === null) {
            throw new RegexException($this->getErrorMessage());
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
            throw new RegexException($this->getErrorMessage());
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
            throw new RegexException($this->getErrorMessage());
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

        $flagChecker = static::getFlagChecker($flags);
        $phpFlags = 0;
        if ($flagChecker->get('noEmpty')) {
            $phpFlags |= PREG_SPLIT_NO_EMPTY;
        } elseif ($flagChecker->get('delimCapture')) {
            $phpFlags |= PREG_SPLIT_DELIM_CAPTURE;
        }

        $result = preg_split($this->pattern, $subject, $limit, $phpFlags);
        if ($result === false) {
            throw new RegexException($this->getErrorMessage());
        }

        $this->count = count($result);

        return $result;
    }

    /**
     * Substitute for preg_split that returns all non-empty pieces.
     *
     * @return list<string>
     * @throws RegexException
     * @throws TypeException
     */
    public function splitAll(string $subject): array
    {
        if (! is_string($this->pattern)) {
            throw new TypeException('String pattern expected');
        }

        $result = preg_split($this->pattern, $subject, -1, self::NO_EMPTY);
        if ($result === false) {
            throw new RegexException($this->getErrorMessage());
        }

        $this->count = count($result);

        return $result;
    }

    protected function getPatternDescription(): string
    {
        if (is_string($this->pattern)) {
            return $this->pattern;
        }

        return implode(', ', $this->pattern);
    }
}
