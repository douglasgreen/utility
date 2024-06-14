<?php

declare(strict_types=1);

namespace DouglasGreen\Utility\Regex;

/**
 * Regex helper class with static functions.
 */
class Regex
{
    /**
     * A simple static matcher that uses preg_match and checks that a match exists.
     */
    public static function hasMatch(string $pattern, string $subject, int $offset = 0): bool
    {
        $matcher = new Matcher($pattern);
        $matcher->match($subject, $offset);
        return $matcher->hasMatch();
    }

    /**
     * A simple static matcher that uses preg_match and returns the match.
     *
     * @return array<string|int, string>
     */
    public static function match(string $pattern, string $subject, int $offset = 0): array
    {
        $matcher = new Matcher($pattern);
        return $matcher->match($subject, $offset);
    }

    /**
     * A simple static matcher that uses preg_match_all and returns the match.
     *
     * Returns [] as a convenience if there are no matches.
     *
     * @return array<string|int, array<int, string>>
     */
    public static function matchAll(string $pattern, string $subject, int $offset = 0): array
    {
        $matcher = new Matcher($pattern);
        $matches = $matcher->matchAll($subject, $offset);
        if ($matcher->hasMatch()) {
            return $matches;
        }

        return [];
    }

    /**
     * Substitute for preg_replace that only returns a string.
     *
     * Subject is limited to a string so it only returns a string.
     *
     * @param list<string>|string $replacement
     * @param list<string>|string $pattern
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
     * Substitute for preg_split that returns the pieces.
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
