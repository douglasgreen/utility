<?php

declare(strict_types=1);

namespace DouglasGreen\Utility;

/**
 * Regex utility class to store information about preg_match_all matches.
 *
 * @phpstan-type MatchAll mixed
 */
class MatchAllArray extends RegexMatch
{
    /**
     * @param array<string|int, MatchAll> $matches
     */
    public function __construct(
        protected array $matches,
        protected int $count
    ) {}

    /**
     * @return MatchAll
     */
    public function get(string|int $key): mixed
    {
        return $this->matches[$key] ?? null;
    }

    /**
     * @return array<string|int, MatchAll>
     */
    public function getAll(): array
    {
        return $this->matches;
    }
}
