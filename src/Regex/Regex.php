<?php

declare(strict_types=1);

namespace DouglasGreen\Utility\Regex;

/**
 * Regex helper class with static functions.
 *
 * These functions call the equivalent functions in Matcher or ArrayMatcher.
 */
class Regex
{
    /**
     * @param list<string>|string $pattern
     * @param list<string>|string $replacement
     */
    public static function filteredReplace(
        array|string $pattern,
        array|string $replacement,
        string $subject,
        int $limit = -1,
    ): ?string {
        $matcher = new Matcher($pattern);
        return $matcher->filteredReplace($replacement, $subject, $limit);
    }

    /**
     * @param list<string>|string $pattern
     * @param list<string>|string $replacement
     * @param list<string> $subject
     * @return array<string>
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

    public static function hasMatch(string $pattern, string $subject, int $offset = 0): bool
    {
        $matcher = new Matcher($pattern);
        return $matcher->hasMatch($subject, $offset);
    }

    /**
     * @return array<string|int, string>
     */
    public static function match(string $pattern, string $subject, int $offset = 0): array
    {
        $matcher = new Matcher($pattern);
        return $matcher->match($subject, $offset);
    }

    /**
     * @return array<string|int, array<int, string>>
     */
    public static function matchAll(string $pattern, string $subject, int $offset = 0): array
    {
        $matcher = new Matcher($pattern);
        return $matcher->matchAll($subject, $offset);
    }

    /**
     * @return array<int, array<string|int, string>>
     */
    public static function matchAllSetOrder(
        string $pattern,
        string $subject,
        int $offset = 0,
    ): array {
        $matcher = new Matcher($pattern);
        return $matcher->matchAllSetOrder($subject, $offset);
    }

    /**
     * @return array<string|int, array<int, array<int, string|int>>>
     */
    public static function matchAllWithOffsets(
        string $pattern,
        string $subject,
        int $offset = 0,
    ): array {
        $matcher = new Matcher($pattern);
        return $matcher->matchAllWithOffsets($subject, $offset);
    }

    /**
     * @return array<string|int, array<int, string|int>>
     */
    public static function matchWithOffsets(
        string $pattern,
        string $subject,
        int $offset = 0,
    ): array {
        $matcher = new Matcher($pattern);
        return $matcher->matchWithOffsets($subject, $offset);
    }

    /**
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
     * @param list<string>|string $pattern
     */
    public static function replaceCall(
        array|string $pattern,
        callable $callback,
        string $subject,
        int $limit = -1,
    ): string {
        $matcher = new Matcher($pattern);
        return $matcher->replaceCall($callback, $subject, $limit);
    }

    /**
     * @param list<string>|string $pattern
     * @param list<string> $subject
     * @return list<string>
     */
    public static function replaceCallList(
        array|string $pattern,
        callable $callback,
        array $subject,
        int $limit = -1,
    ): array {
        $matcher = new Matcher($pattern);
        return $matcher->replaceCallList($callback, $subject, $limit);
    }

    /**
     * @param array<string, callable> $pattern
     */
    public static function replaceCallMap(array $pattern, string $subject, int $limit = -1): string
    {
        $arrayMatcher = new ArrayMatcher($pattern);
        return $arrayMatcher->replaceCallMap($subject, $limit);
    }

    /**
     * @param array<string, callable> $pattern
     * @param list<string> $subject
     * @return array<string>
     */
    public static function replaceCallMapList(
        array $pattern,
        array $subject,
        int $limit = -1,
    ): array {
        $arrayMatcher = new ArrayMatcher($pattern);
        return $arrayMatcher->replaceCallMapList($subject, $limit);
    }

    /**
     * @param list<string>|string $pattern
     * @param list<string>|string $replacement
     * @param list<string> $subject
     * @return array<string>
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
     * @param list<string> $array
     * @return array<string>
     */
    public static function searchList(string $pattern, array $array): array
    {
        $matcher = new Matcher($pattern);
        return $matcher->searchList($array);
    }

    /**
     * @param list<string> $array
     * @return array<string>
     */
    public static function searchListInverted(string $pattern, array $array): array
    {
        $matcher = new Matcher($pattern);
        return $matcher->searchListInverted($array);
    }

    /**
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

    /**
     * @return list<string>
     */
    public static function splitAll(string $pattern, string $subject): array
    {
        $matcher = new Matcher($pattern);
        return $matcher->splitAll($subject);
    }
}
