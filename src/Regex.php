<?php

declare(strict_types=1);

namespace DouglasGreen\Utility;

use DouglasGreen\Utility\Exceptions\Data\TypeException;
use DouglasGreen\Utility\Exceptions\Process\RegexException;

/**
 * Regex utility class to throw exceptions when basic operations fail.
 *
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class Regex
{
    /**
     * A simple static matcher that uses preg_match_all and returns the match.
     *
     * @return array<string|int, mixed>
     */
    public static function getAllMatches(
        string $pattern,
        string $subject,
        int $flags = 0,
        int $offset = 0,
    ): array {
        $regex = new self($pattern);
        return $regex->matchAll($subject, $flags, $offset)
            ->getAll();
    }

    /**
     * A simple static matcher that uses preg_match and returns the match.
     *
     * @param 0|256|512|768 $flags
     * @return array<string|int, mixed>
     */
    public static function getMatch(
        string $pattern,
        string $subject,
        int $flags = 0,
        int $offset = 0,
    ): array {
        $regex = new self($pattern);
        return $regex->match($subject, $flags, $offset)
            ->getAll();
    }

    /**
     * Substitute for preg_replace that only returns a string.
     *
     * Subject is limited to a string so it only returns a string.
     *
     * @param list<string>|string $replacement
     * @param list<string>|string $pattern
     */
    public function getReplace(
        array|string $pattern,
        array|string $replacement,
        string $subject,
        int $limit = -1,
    ): string {
        $regex = new self($pattern);
        return $regex->replace($replacement, $subject, $limit);
    }

    /**
     * A simple static matcher that uses preg_match and checks that a match exists.
     *
     * @param 0|256|512|768 $flags
     */
    public static function hasMatch(
        string $pattern,
        string $subject,
        int $flags = 0,
        int $offset = 0,
    ): bool {
        $regex = new self($pattern);
        return $regex->match($subject, $flags, $offset)
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
    public function filterList(array $array, int $flags = 0): MatchList
    {
        if (! is_string($this->pattern)) {
            throw new TypeException('String pattern expected');
        }

        $result = preg_grep($this->pattern, $array, $flags);
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
     * @param 0|256|512|768 $flags
     * @throws RegexException
     * @throws TypeException
     */
    public function match(
        string $subject,
        int $flags = 0,
        int $offset = 0,
    ): MatchArray {
        if (! is_string($this->pattern)) {
            throw new TypeException('String pattern expected');
        }

        $result = preg_match($this->pattern, $subject, $match, $flags, $offset);
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
        int $flags = 0,
        int $offset = 0,
    ): MatchAllArray {
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
            throw new RegexException('Regex failed: ' . $this->pattern);
        }

        return new MatchAllArray($matches, $result);
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
        int $flags = 0,
    ): string {
        $result = preg_replace_callback(
            $this->pattern,
            $callback,
            $subject,
            $limit,
            $flags,
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
        int $flags = 0,
    ): MatchList {
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

        return new MatchList($result, $count);
    }

    /**
     * Substitute for preg_split.
     *
     * @throws RegexException
     * @throws TypeException
     */
    public function split(
        string $subject,
        int $limit = -1,
        int $flags = 0,
    ): MatchList {
        if (! is_string($this->pattern)) {
            throw new TypeException('String pattern expected');
        }

        $result = preg_split($this->pattern, $subject, $limit, $flags);
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
