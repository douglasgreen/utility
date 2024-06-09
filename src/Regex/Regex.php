<?php

declare(strict_types=1);

namespace DouglasGreen\Utility\Regex;

use DouglasGreen\Utility\Exceptions\Data\TypeException;
use DouglasGreen\Utility\Exceptions\Process\RegexException;

/**
 * Regex utility class to throw exceptions when basic operations fail.
 *
 * @phpstan-import-type Match from MatchArray
 * @phpstan-import-type MatchOffset from MatchOffsetArray
 * @phpstan-import-type MatchAll from MatchAllArray
 *
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class Regex
{
    /**
     * Substitute for preg_replace that only returns a string.
     *
     * Subject is limited to a string so it only returns a string.
     *
     * @param list<string>|string $replacement
     * @param list<string>|string $pattern
     */
    public static function doReplace(
        array|string $pattern,
        array|string $replacement,
        string $subject,
        int $limit = -1,
    ): string {
        $regex = new self($pattern);
        return $regex->replace($replacement, $subject, $limit);
    }

    /**
     * Substitute for preg_split that returns the pieces.
     *
     * @return list<string>
     */
    public static function doSplit(
        string $pattern,
        string $subject,
        int $limit = -1
    ): array {
        $regex = new self($pattern);
        return $regex->split($subject, $limit)
            ->getAll();
    }

    /**
     * A simple static matcher that uses preg_match_all and returns the match.
     *
     * @return array<string|int, MatchAll>
     */
    public static function getAllMatches(
        string $pattern,
        string $subject,
        int $offset = 0,
    ): array {
        $regex = new self($pattern);
        return $regex->matchAll($subject, $offset)
            ->getAll();
    }

    /**
     * A simple static matcher that uses preg_match and returns the match.
     *
     * @return array<string|int, Match>
     */
    public static function getMatch(
        string $pattern,
        string $subject,
        int $offset = 0,
    ): array {
        $regex = new self($pattern);
        return $regex->match($subject, $offset)
            ->getAll();
    }

    /**
     * A simple static matcher that uses preg_match and checks that a match exists.
     */
    public static function hasMatch(
        string $pattern,
        string $subject,
        int $offset = 0,
    ): bool {
        $regex = new self($pattern);
        return $regex->match($subject, $offset)
            ->has();
    }

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
     * @throws TypeException
     */
    public function filterList(array $array): MatchList
    {
        if (! is_string($this->pattern)) {
            throw new TypeException('String pattern expected');
        }

        $result = preg_grep($this->pattern, $array);
        if ($result === false) {
            throw new RegexException('Regex failed: ' . $this->pattern);
        }

        return new MatchList($result, count($result));
    }

    /**
     * Substitute for preg_grep that inverts the match.
     *
     * The name indicates it is filtering the array and shouldn't be confused
     * with preg_filter.
     *
     * @param list<string> $array
     * @throws RegexException
     * @throws TypeException
     */
    public function filterListInverted(array $array): MatchList
    {
        if (! is_string($this->pattern)) {
            throw new TypeException('String pattern expected');
        }

        $result = preg_grep($this->pattern, $array, PREG_GREP_INVERT);
        if ($result === false) {
            throw new RegexException('Regex failed: ' . $this->pattern);
        }

        return new MatchList($result, count($result));
    }

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
        $result = preg_filter(
            $this->pattern,
            $replacement,
            $subject,
            $limit
        );

        if ($result === null) {
            throw new RegexException('Regex failed: ' . $this->getPattern());
        }

        return $result;
    }

    /**
     * Substitute for preg_filter.
     *
     * The function name indicates that preg_filter is identical to preg_replace
     * except it only returns the subjects where there was a match.
     *
     * Takes an array as $subject so it returns a MatchList.
     *
     * preg_filter doesn't distinguish between no matches and error when using
     * array as subject, so no exception can be thrown here.
     *
     * @param list<string>|string $replacement
     * @param list<string> $subject
     */
    public function filteredReplaceList(
        array|string $replacement,
        array $subject,
        int $limit = -1,
    ): MatchList {
        $result = preg_filter(
            $this->pattern,
            $replacement,
            $subject,
            $limit,
            $count
        );

        return new MatchList($result, $count);
    }

    /**
     * Do a preg_match and store the results.
     *
     * @throws RegexException
     * @throws TypeException
     */
    public function match(string $subject, int $offset = 0): MatchArray
    {
        if (! is_string($this->pattern)) {
            throw new TypeException('String pattern expected');
        }

        $result = preg_match($this->pattern, $subject, $match, 0, $offset);
        if ($result === false) {
            throw new RegexException('Regex failed: ' . $this->pattern);
        }

        return new MatchArray($match, $result);
    }

    /**
     * Do a preg_match_all and store the results.
     *
     * @throws RegexException
     * @throws TypeException
     */
    public function matchAll(
        string $subject,
        int $offset = 0,
    ): MatchAllArray {
        if (! is_string($this->pattern)) {
            throw new TypeException('String pattern expected');
        }

        $result = preg_match_all(
            $this->pattern,
            $subject,
            $matches,
            0,
            $offset
        );

        if ($result === false) {
            throw new RegexException('Regex failed: ' . $this->pattern);
        }

        return new MatchAllArray($matches, $result);
    }

    /**
     * Do a preg_match_all with offset capture and store the results.
     *
     * @throws RegexException
     * @throws TypeException
     */
    public function matchAllOffset(
        string $subject,
        int $offset = 0,
    ): MatchAllOffsetArray {
        if (! is_string($this->pattern)) {
            throw new TypeException('String pattern expected');
        }

        $result = preg_match_all(
            $this->pattern,
            $subject,
            $matches,
            PREG_OFFSET_CAPTURE,
            $offset
        );

        if ($result === false) {
            throw new RegexException('Regex failed: ' . $this->pattern);
        }

        return new MatchAllOffsetArray($matches, $result);
    }

    /**
     * Do a preg_match with offset capture and store the results.
     *
     * @throws RegexException
     * @throws TypeException
     */
    public function matchOffset(
        string $subject,
        int $offset = 0,
    ): MatchOffsetArray {
        if (! is_string($this->pattern)) {
            throw new TypeException('String pattern expected');
        }

        $result = preg_match(
            $this->pattern,
            $subject,
            $match,
            PREG_OFFSET_CAPTURE,
            $offset
        );
        if ($result === false) {
            throw new RegexException('Regex failed: ' . $this->pattern);
        }

        return new MatchOffsetArray($match, $result);
    }

    /**
     * Substitute for preg_replace.
     *
     * Takes a string as $subject so it returns a string.
     *
     * @param list<string>|string $replacement
     * @throws RegexException
     */
    public function replace(
        array|string $replacement,
        string $subject,
        int $limit = -1,
    ): string {
        $result = preg_replace(
            $this->pattern,
            $replacement,
            $subject,
            $limit,
        );

        if ($result === null) {
            throw new RegexException('Regex failed: ' . $this->getPattern());
        }

        return $result;
    }

    /**
     * Substitute for preg_replace.
     *
     * Takes an array as $subject so it returns a MatchList.
     *
     * @param list<string>|string $replacement
     * @param list<string> $subject
     * @throws RegexException
     */
    public function replaceList(
        array|string $replacement,
        array $subject,
        int $limit = -1,
    ): MatchList {
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

        return new MatchList($result, $count);
    }

    /**
     * Substitute for preg_replace_callback.
     *
     * Takes a string as $subject so it returns a string.
     *
     * @throws RegexException
     */
    public function replaceCall(
        callable $callback,
        string $subject,
        int $limit = -1,
    ): string {
        $result = preg_replace_callback(
            $this->pattern,
            $callback,
            $subject,
            $limit,
        );

        if ($result === null) {
            throw new RegexException('Regex failed: ' . $this->getPattern());
        }

        return $result;
    }

    /**
     * Substitute for preg_replace_callback.
     *
     * Takes an array as $subject so it returns a MatchList.
     *
     * @param list<string> $subject
     * @throws RegexException
     */
    public function replaceCallList(
        callable $callback,
        array $subject,
        int $limit = -1,
    ): MatchList {
        $result = preg_replace_callback(
            $this->pattern,
            $callback,
            $subject,
            $limit,
            $count,
        );

        if ($result === null) {
            throw new RegexException('Regex failed: ' . $this->getPattern());
        }

        return new MatchList($result, $count);
    }

    /**
     * Substitute for preg_split.
     *
     * @throws RegexException
     * @throws TypeException
     */
    public function split(string $subject, int $limit = -1): MatchList
    {
        if (! is_string($this->pattern)) {
            throw new TypeException('String pattern expected');
        }

        $result = preg_split($this->pattern, $subject, $limit);
        if ($result === false) {
            throw new RegexException('Regex failed: ' . $this->pattern);
        }

        return new MatchList($result, count($result));
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
