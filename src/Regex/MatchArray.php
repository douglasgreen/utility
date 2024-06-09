<?php

declare(strict_types=1);

namespace DouglasGreen\Utility\Regex;

/**
 * Regex utility class to store information about preg_match matches.
 *
 * @phpstan-type Match string
 */
class MatchArray extends RegexMatch
{
    /**
     * @param array<string|int, Match> $matches
     */
    public function __construct(
        protected array $matches,
        protected int $count
    ) {}

    /**
     * @return Match
     */
    public function get(string|int $key): string|null
    {
        return $this->matches[$key] ?? null;
    }

    /**
     * @return array<string|int, Match>
     */
    public function getAll(): array
    {
        return $this->matches;
    }
}
