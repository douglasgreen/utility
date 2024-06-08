<?php

declare(strict_types=1);

namespace DouglasGreen\Utility;

/**
 * Regex utility class to store information about matches.
 */
class RegexMatch
{
    /**
     * @param array<int, mixed>|string|null $matches
     */
    public function __construct(
        protected array|string|null $matches,
        protected ?int $count
    ) {}

    public function count(): ?int
    {
        return $this->count;
    }

    /**
     * @return array<int, mixed>|string|null
     */
    public function get(): array|string|null
    {
        return $this->matches;
    }

    public function has(): bool
    {
        return $this->count > 0;
    }

    public function not(): bool
    {
        return $this->count === 0 || $this->count === null;
    }
}
