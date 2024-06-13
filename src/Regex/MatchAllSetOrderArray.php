<?php

declare(strict_types=1);

namespace DouglasGreen\Utility\Regex;

/**
 * Regex utility class to store information about preg_match_all matches with PREG_SET_ORDER flag.
 *
 * @phpstan-type MatchAllSetOrder array<string|int, string>
 */
class MatchAllSetOrderArray extends RegexMatch
{
    /**
     * @param array<int, MatchAllSetOrder> $matches
     */
    public function __construct(
        protected array $matches,
        protected int $count
    ) {}

    /**
     * @return ?MatchAllSetOrder
     */
    public function get(int $key): ?array
    {
        return $this->matches[$key] ?? null;
    }

    /**
     * @return array<int, MatchAllSetOrder>
     */
    public function getAll(): array
    {
        return $this->matches;
    }
}
