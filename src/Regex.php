<?php

declare(strict_types=1);

namespace DouglasGreen\Utility;

use DouglasGreen\Utility\Exceptions\Data\TypeException;
use DouglasGreen\Utility\Exceptions\Process\RegexException;

/**
 * Regex utility class to throw exceptions when basic operations fail.
 *
 * No replacement is provided for preg_filter with array arguments because it
 * returns array on regex failure or no matches and so no distinction can be
 * made.
 */
class Regex
{
    /**
     * @param list<string>|string $pattern
     */
    public function __construct(
        protected array|string $pattern
    ) {}

    /**
     * Substitute for preg_grep.
     *
     * The name indicates it is filtering the array and shouldn't be confused
     * with preg_filter.
     *
     * @param list<string> $array
     * @throws RegexException
     */
    public function filterArray(array $array, int $flags = 0): RegexMatch
    {
        if (! is_string($this->pattern)) {
            throw new TypeException('String pattern expected');
        }

        $result = preg_grep($this->pattern, $array, $flags);
        if ($result === false) {
            throw new RegexException('Regex failed: ' . $this->getPattern());
        }

        return new RegexMatch($result, count($result));
    }

    /**
     * Substitute for preg_filter.
     *
     * The function name indicates that preg_filter is identical to preg_replace
     * except it only returns the subjects where there was a match.
     *
     * @param list<string>|string $replacement
     * @param list<string>|string $subject
     * @throws RegexException
     */
    public function filteredReplace(
        array|string $replacement,
        array|string $subject,
        int $limit = -1,
        int &$count = null,
    ): RegexMatch {
        $result = preg_filter(
            $this->pattern,
            $replacement,
            $subject,
            $limit,
            $count
        );

        if ($result === null) {
            throw new RegexException('Regex failed: ' . $this->getPattern());
        }

        return new RegexMatch($result, $count);
    }

    /**
     * Do a preg_match and store the results.
     *
     * @param 0|256|512|768 $flags
     * @throws RegexException
     * @throws TypeException
     */
    public function match(
        string $subject,
        int $flags = 0,
        int $offset = 0,
    ): RegexMatch {
        if (! is_string($this->pattern)) {
            throw new TypeException('String pattern expected');
        }

        $result = preg_match($this->pattern, $subject, $match, $flags, $offset);
        if ($result === false) {
            throw new RegexException('Regex failed: ' . $this->getPattern());
        }

        return new RegexMatch($match, $result);
    }

    /**
     * Do a preg_match_all and store the results.
     *
     * @throws RegexException
     * @throws TypeException
     */
    public function matchAll(
        string $subject,
        int $flags = 0,
        int $offset = 0,
    ): RegexMatch {
        if (! is_string($this->pattern)) {
            throw new TypeException('String pattern expected');
        }

        $result = preg_match_all(
            $this->pattern,
            $subject,
            $matches,
            $flags,
            $offset
        );

        if ($result === false) {
            throw new RegexException('Regex failed: ' . $this->getPattern());
        }

        return new RegexMatch($matches, $result);
    }

    /**
     * Substitute for preg_replace.
     *
     * @param list<string> $replacement
     * @param list<string> $subject
     * @throws RegexException
     */
    public function replace(
        array|string $replacement,
        array|string $subject,
        int $limit = -1,
        int &$count = null,
    ): RegexMatch {
        $result = preg_replace(
            $this->pattern,
            $replacement,
            $subject,
            $limit,
            $count,
        );

        if ($result === null) {
            throw new RegexException('Regex failed: ' . $this->getPattern());
        }

        return new RegexMatch($result, $count);
    }

    /**
     * Substitute for preg_replace_callback.
     *
     * @param list<string> $subject
     * @throws RegexException
     */
    public function replaceCall(
        callable $callback,
        array|string $subject,
        int $limit = -1,
        int &$count = null,
        int $flags = 0,
    ): RegexMatch {
        $result = preg_replace_callback(
            $this->pattern,
            $callback,
            $subject,
            $limit,
            $count,
            $flags,
        );

        if ($result === null) {
            throw new RegexException('Regex failed: ' . $this->getPattern());
        }

        return new RegexMatch($result, $count);
    }

    /**
     * Substitute for preg_split.
     *
     * @throws RegexException
     */
    public function split(
        string $subject,
        int $limit = -1,
        int $flags = 0,
    ): RegexMatch {
        if (! is_string($this->pattern)) {
            throw new TypeException('String pattern expected');
        }

        $result = preg_split($this->pattern, $subject, $limit, $flags);
        if ($result === false) {
            throw new RegexException('Regex failed: ' . $this->getPattern());
        }

        return new RegexMatch($result, count($result));
    }

    /**
     * @throws RegexException
     */
    protected function getPattern(): string
    {
        return is_array($this->pattern) ? implode(
            ', ',
            $this->pattern
        ) : $this->pattern;
    }
}
