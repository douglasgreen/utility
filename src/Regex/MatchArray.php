<?php

declare(strict_types=1);

namespace DouglasGreen\Utility\Regex;

/**
 * Regex utility class to store information about preg_match matches.
 */
class MatchArray extends RegexMatch
{
    /**
     * @param array<string|int, string> $matches
     */
    public function __construct(
        protected array $matches,
        protected int $count
    ) {}

    public function get(string|int $key): string|null
    {
        return $this->matches[$key] ?? null;
    }

    /**
     * @return array<string|int, string>
     */
    public function getAll(): array
    {
        return $this->matches;
    }
}
