<?php

declare(strict_types=1);

namespace DouglasGreen\Utility\Regex;

/**
 * Regex utility class to store information about preg_match matches.
 *
 * @phpstan-type MatchOffset array<int, string|int>
 */
class MatchOffsetArray extends RegexMatch
{
    /**
     * @param array<string|int, MatchOffset> $matches
     */
    public function __construct(
        protected array $matches,
        protected int $count
    ) {}

    /**
     * @return ?MatchOffset
     */
    public function get(string|int $key): ?array
    {
        return $this->matches[$key] ?? null;
    }

    /**
     * @return array<string|int, MatchOffset>
     */
    public function getAll(): array
    {
        return $this->matches;
    }
}
