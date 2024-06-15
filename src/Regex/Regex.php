<?php

declare(strict_types=1);

namespace DouglasGreen\Utility\Regex;

/**
 * Regex helper class with static functions.
 */
class Regex
{
    /* Same consonants as Matcher class */
    public const DELIM_CAPTURE = 1;

    public const NO_EMPTY = 2;

    /**
     * Static function that calls the equivalent function in the Matcher class.
     *
     * @param list<string>|string $pattern
     * @param list<string>|string $replacement
     * @throws RegexException
     */
    public static function filteredReplace(
        array|string $pattern,
        array|string $replacement,
        string $subject,
        int $limit = -1,
    ): string {
        $matcher = new Matcher($pattern);
        return $matcher->filteredReplace($replacement, $subject, $limit);
    }

    /**
     * Static function that calls the equivalent function in the Matcher class.
     *
     * @param list<string>|string $pattern
     * @param list<string>|string $replacement
     * @param list<string> $subject
     * @return list<string>
     * @throws RegexException
     */
    public static function filteredReplaceList(
        array|string $pattern,
        array|string $replacement,
        array $subject,
        int $limit = -1,
    ): array {
        $matcher = new Matcher($pattern);
        return $matcher->filteredReplaceList($replacement, $subject, $limit);
    }

    /**
     * Static function that uses preg_match and checks that a match exists.
     */
    public static function hasMatch(string $pattern, string $subject, int $offset = 0): bool
    {
        $matcher = new Matcher($pattern);
        $matcher->match($subject, $offset);
        return $matcher->hasMatch();
    }

    /**
     * Static function that calls the equivalent function in the Matcher class.
     *
     * @return array<string|int, string>
     */
    public static function match(string $pattern, string $subject, int $offset = 0): array
    {
        $matcher = new Matcher($pattern);
        return $matcher->match($subject, $offset);
    }

    /**
     * Static function that calls the equivalent function in the Matcher class.
     *
     * @return array<string|int, array<int, string>>
     */
    public static function matchAll(string $pattern, string $subject, int $offset = 0): array
    {
        $matcher = new Matcher($pattern);
        return $matcher->matchAll($subject, $offset);
    }

    /**
     * Static function that calls the equivalent function in the Matcher class.
     *
     * @return array<int, array<string|int, string>>
     * @throws RegexException
     * @throws TypeException
     */
    public static function matchAllSetOrder(
        string $pattern,
        string $subject,
        int $offset = 0
    ): array {
        $matcher = new Matcher($pattern);
        return $matcher->matchAllSetOrder($subject, $offset);
    }

    /**
     * Static function that calls the equivalent function in the Matcher class.
     *
     * @return array<string|int, array<int, array<int, string|int>>>
     * @throws RegexException
     * @throws TypeException
     */
    public static function matchAllWithOffsets(
        string $pattern,
        string $subject,
        int $offset = 0
    ): array {
        $matcher = new Matcher($pattern);
        return $matcher->matchAllWithOffsets($subject, $offset);
    }

    /**
     * Static function that calls the equivalent function in the Matcher class.
     *
     * @return array<string|int, array<int, string|int>>
     * @throws RegexException
     * @throws TypeException
     */
    public static function matchWithOffsets(
        string $pattern,
        string $subject,
        int $offset = 0
    ): array {
        $matcher = new Matcher($pattern);
        return $matcher->matchWithOffsets($subject, $offset);
    }

    /**
     * Static function that calls the equivalent function in the Matcher class.
     *
     * @param list<string>|string $pattern
     * @param list<string>|string $replacement
     */
    public static function replace(
        array|string $pattern,
        array|string $replacement,
        string $subject,
        int $limit = -1,
    ): string {
        $matcher = new Matcher($pattern);
        return $matcher->replace($replacement, $subject, $limit);
    }

    /**
     * Static function that calls the equivalent function in the Matcher class.
     *
     * @param list<string>|string $pattern
     * @throws RegexException
     */
    public static function replaceCall(
        array|string $pattern,
        callable $callback,
        string $subject,
        int $limit = -1
    ): string {
        $matcher = new Matcher($pattern);
        return $matcher->replaceCall($callback, $subject, $limit);
    }

    /**
     * Static function that calls the equivalent function in the Matcher class.
     *
     * list<string>|string $pattern
     * @param list<string> $subject
     * @return list<string>
     * @throws RegexException
     */
    public static function replaceCallList(
        array|string $pattern,
        callable $callback,
        array $subject,
        int $limit = -1
    ): array {
        $matcher = new Matcher($pattern);
        return $matcher->replaceCallList($callback, $subject, $limit);
    }

    /**
     * Static function that calls the equivalent function in the Matcher class.
     *
     * @param array<string, callable> $pattern
     * @throws RegexException
     */
    public static function replaceCallMap(array $pattern, string $subject, int $limit = -1): string
    {
        $matcher = new Matcher($pattern);
        return $matcher->replaceCallMap($subject, $limit);
    }

    /**
     * Static function that calls the equivalent function in the Matcher class.
     *
     * @param array<string, callable> $pattern
     * @param list<string> $subject
     * @throws RegexException
     */
    public static function replaceCallMapList(
        array $pattern,
        array $subject,
        int $limit = -1
    ): array {
        $matcher = new Matcher($pattern);
        return $matcher->replaceCallMapList($subject, $limit);
    }

    /**
     * Static function that calls the equivalent function in the Matcher class.
     *
     * @param list<string>|string $pattern
     * @param list<string>|string $replacement
     * @param list<string> $subject
     * @return list<string>
     * @throws RegexException
     */
    public static function replaceList(
        array|string $pattern,
        array|string $replacement,
        array $subject,
        int $limit = -1,
    ): array {
        $matcher = new Matcher($pattern);
        return $matcher->replaceList($replacement, $subject, $limit);
    }

    /**
     * Static function that calls the equivalent function in the Matcher class.
     *
     * @param list<string> $array
     * @return list<string>
     * @throws RegexException
     * @throws TypeException
     */
    public static function searchList(string $pattern, array $array): array
    {
        $matcher = new Matcher($pattern);
        return $matcher->searchList($array);
    }

    /**
     * Static function that calls the equivalent function in the Matcher class.
     *
     * @param list<string> $array
     * @return list<string>
     * @throws RegexException
     * @throws TypeException
     */
    public static function searchListInverted(string $pattern, array $array): array
    {
        $matcher = new Matcher($pattern);
        return $matcher->searchListInverted($array);
    }

    /**
     * Static function that calls the equivalent function in the Matcher class.
     *
     * @return list<string>
     */
    public static function split(
        string $pattern,
        string $subject,
        int $limit = -1,
        int $flags = 0,
    ): array {
        $matcher = new Matcher($pattern);
        return $matcher->split($subject, $limit, $flags);
    }
}
