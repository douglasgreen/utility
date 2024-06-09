<?php

declare(strict_types=1);

namespace DouglasGreen\Utility;

/**
 * Regex utility class to store information about preg_match_all matches.
 */
class MatchAllArray extends RegexMatch
{
    /**
     * @param array<string|int, mixed> $matches
     */
    public function __construct(
        protected array $matches,
        protected int $count
    ) {}

    public function get(string|int $key): mixed
    {
        return $this->matches[$key] ?? null;
    }

    /**
     * @return array<string|int, mixed>
     */
    public function getAll(): array
    {
        return $this->matches;
    }
}
