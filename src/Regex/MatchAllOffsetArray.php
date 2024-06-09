<?php

declare(strict_types=1);

namespace DouglasGreen\Utility\Regex;

/**
 * Regex utility class to store information about preg_match_all matches.
 *
 * @phpstan-type MatchAllOffset array<int, array<int, string|int>>
 */
class MatchAllOffsetArray extends RegexMatch
{
    /**
     * @param array<string|int, MatchAllOffset> $matches
     */
    public function __construct(
        protected array $matches,
        protected int $count
    ) {}

    /**
     * @return ?MatchAllOffset
     */
    public function get(string|int $key): ?array
    {
        return $this->matches[$key] ?? null;
    }

    /**
     * @return array<string|int, MatchAllOffset>
     */
    public function getAll(): array
    {
        return $this->matches;
    }
}
