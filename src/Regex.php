<?php

declare(strict_types=1);

namespace DouglasGreen\Utility;

use DouglasGreen\Utility\Exceptions\Process\RegexException;

/**
 * Regex utility class to throw exceptions when basic operations fail.
 *
 * No replacement is provided for preg_filter with array argumnts because it
 * returns array on regex failure or no matches and so no distinction can be
 * made.
 *
 * @todo Add preg_replace_callback and preg_replace_callback_array.
 */
class Regex
{
    /**
     * Substitute for counting the matches of preg_match_all.
     *
     * @throws RegexException
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public static function countMatches(
        string $pattern,
        string $subject,
        int $flags = 0,
        int $offset = 0,
    ): int {
        $result = preg_match_all($pattern, $subject, $matches, $flags, $offset);
        if ($result === false) {
            throw new RegexException('Regex failed: ' . $pattern);
        }

        return $result;
    }

    /**
     * Substitute for preg_filter with string arguments.
     * @throws RegexException
     */
    public static function filter(
        string $pattern,
        string $replacement,
        string $subject,
        int $limit = -1,
        int &$count = null,
    ): string {
        $result = preg_filter($pattern, $replacement, $subject, $limit, $count);
        if ($result === null) {
            throw new RegexException('Regex failed: ' . $pattern);
        }

        return $result;
    }

    /**
     * Substitute for preg_match_all that returns non-null numbered matches.
     *
     * @return array<array<int, string>>
     * @throws RegexException
     */
    public static function getAllMatches(
        string $pattern,
        string $subject,
        int $flags = 0,
        int $offset = 0,
    ): array {
        $result = preg_match_all($pattern, $subject, $matches, $flags, $offset);
        if ($result === false) {
            throw new RegexException('Regex failed: ' . $pattern);
        }

        $filteredMatches = [];
        foreach ($matches as $key1 => $values) {
            if (! is_int($key1)) {
                continue;
            }

            foreach ($values as $key2 => $value) {
                if (! is_int($key2)) {
                    continue;
                }

                if (! is_string($value)) {
                    continue;
                }

                $filteredMatches[$key1][$key2] = $value;
            }
        }

        return $filteredMatches;
    }

    /**
     * Substitute for preg_match_all that returns non-null named matches.
     *
     * @return array<array<string, string>>
     * @throws RegexException
     */
    public static function getAllNamedMatches(
        string $pattern,
        string $subject,
        int $flags = 0,
        int $offset = 0,
    ): array {
        $result = preg_match_all($pattern, $subject, $matches, $flags, $offset);
        if ($result === false) {
            throw new RegexException('Regex failed: ' . $pattern);
        }

        $filteredMatches = [];
        foreach ($matches as $key1 => $values) {
            if (! is_int($key1)) {
                continue;
            }

            foreach ($values as $key2 => $value) {
                if (! is_string($key2)) {
                    continue;
                }

                if (! is_string($value)) {
                    continue;
                }

                $filteredMatches[$key1][$key2] = $value;
            }
        }

        return $filteredMatches;
    }

    /**
     * Substitute for preg_match that returns non-null numbered matches.
     *
     * @param 0|256|512|768 $flags
     * @return array<int, string>
     * @throws RegexException
     */
    public static function getMatch(
        string $pattern,
        string $subject,
        int $flags = 0,
        int $offset = 0,
    ): array {
        $result = preg_match($pattern, $subject, $matches, $flags, $offset);
        if ($result === false) {
            throw new RegexException('Regex failed: ' . $pattern);
        }

        $filteredMatches = [];
        foreach ($matches as $key => $value) {
            if (! is_int($key)) {
                continue;
            }

            if (! is_string($value)) {
                continue;
            }

            $filteredMatches[$key] = $value;
        }

        return $filteredMatches;
    }

    /**
     * Substitute for preg_match that returns non-null named matches.
     *
     * @param 0|256|512|768 $flags
     * @return array<string, string>
     * @throws RegexException
     */
    public static function getNamedMatches(
        string $pattern,
        string $subject,
        int $flags = 0,
        int $offset = 0,
    ): array {
        $result = preg_match($pattern, $subject, $matches, $flags, $offset);
        if ($result === false) {
            throw new RegexException('Regex failed: ' . $pattern);
        }

        $filteredMatches = [];
        foreach ($matches as $key => $value) {
            if (! is_string($key)) {
                continue;
            }

            if (! is_string($value)) {
                continue;
            }

            $filteredMatches[$key] = $value;
        }

        return $filteredMatches;
    }

    /**
     * Substitute for preg_grep.
     *
     * @param list<string> $array
     * @return list<string>
     * @throws RegexException
     */
    public static function grep(
        string $pattern,
        array $array,
        int $flags = 0,
    ): array {
        $result = preg_grep($pattern, $array, $flags);
        if ($result === false) {
            throw new RegexException('Regex failed: ' . $pattern);
        }

        return $result;
    }

    /**
     * Substitute for preg_match that returns bool
     *
     * @param 0|256|512|768 $flags
     * @throws RegexException
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public static function hasMatch(
        string $pattern,
        string $subject,
        int $flags = 0,
        int $offset = 0,
    ): bool {
        $result = preg_match($pattern, $subject, $match, $flags, $offset);
        if ($result === false) {
            throw new RegexException('Regex failed: ' . $pattern);
        }

        return $result !== 0;
    }

    /**
     * Substitute for preg_replace with string arguments.
     *
     * @throws RegexException
     */
    public static function replace(
        string $pattern,
        string $replacement,
        string $subject,
        int $limit = -1,
        int &$count = null,
    ): string {
        $result = preg_replace(
            $pattern,
            $replacement,
            $subject,
            $limit,
            $count,
        );
        if ($result === null) {
            throw new RegexException('Regex failed: ' . $pattern);
        }

        return $result;
    }

    /**
     * Substitute for preg_replace with array arguments.
     *
     * @param list<string> $patterns
     * @param list<string> $replacement
     * @param list<string> $subjects
     * @return list<string>
     * @throws RegexException
     */
    public static function replaceArray(
        array $patterns,
        array $replacement,
        array $subjects,
        int $limit = -1,
        int &$count = null,
    ): array {
        $result = preg_replace(
            $patterns,
            $replacement,
            $subjects,
            $limit,
            $count,
        );
        if ($result === null) {
            throw new RegexException(
                'Regex failed: ' . implode('; ', $patterns),
            );
        }

        return $result;
    }

    /**
     * Substitute for preg_replace_callback with string arguments.
     *
     * @throws RegexException
     */
    public function replaceCall(
        string $pattern,
        callable $callback,
        string $subject,
        int $limit = -1,
        int &$count = null,
        int $flags = 0,
    ): string {
        $result = preg_replace_callback(
            $pattern,
            $callback,
            $subject,
            $limit,
            $count,
            $flags,
        );
        if ($result === null) {
            throw new RegexException('Regex failed: ' . $pattern);
        }

        return $result;
    }

    /**
     * Substitute for preg_replace_callback with array arguments.
     *
     * @param list<string> $patterns
     * @param list<string> $subjects
     * @return list<string>
     * @throws RegexException
     */
    public function replaceCallArray(
        array $patterns,
        callable $callback,
        array $subjects,
        int $limit = -1,
        int &$count = null,
        int $flags = 0,
    ): array {
        $result = preg_replace_callback(
            $patterns,
            $callback,
            $subjects,
            $limit,
            $count,
            $flags,
        );
        if ($result === null) {
            throw new RegexException(
                'Regex failed: ' . implode(';', $patterns),
            );
        }

        return $result;
    }

    /**
     * Substitute for preg_split.
     *
     * @return list<string>
     * @throws RegexException
     */
    public static function split(
        string $pattern,
        string $subject,
        int $limit = -1,
        int $flags = 0,
    ): array {
        $result = preg_split($pattern, $subject, $limit, $flags);
        if ($result === false) {
            throw new RegexException('Regex failed: ' . $pattern);
        }

        return $result;
    }
}
