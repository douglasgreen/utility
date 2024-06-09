<?php

declare(strict_types=1);

namespace DouglasGreen\Utility;

/**
 * Regex utility class to store information about match of simple lists.
 */
class MatchList extends RegexMatch
{
    /**
     * @param list<string> $matches
     */
    public function __construct(
        protected array $matches,
        protected int $count
    ) {}

    public function get(int $key): ?string
    {
        return $this->matches[$key] ?? null;
    }

    /**
     * @return list<string>
     */
    public function getAll(): array
    {
        return $this->matches;
    }
}
