<?php

declare(strict_types=1);

namespace DouglasGreen\Utility\Regex;

/**
 * Regex utility class to store information about matches.
 */
abstract class RegexMatch
{
    /**
     * @var array<string|int, mixed>
     */
    protected array $matches;

    protected int $count;

    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * Get a value that is known to be a string.
     */
    public function getString(string|int $key): ?string
    {
        if (! isset($this->matches[$key])) {
            return null;
        }

        if (! is_string($this->matches[$key])) {
            return null;
        }

        return $this->matches[$key];
    }

    /**
     * Get a value that is known to be an array.
     *
     * @return array<string|int, mixed>
     */
    public function getArray(string|int $key): ?array
    {
        if (! isset($this->matches[$key])) {
            return null;
        }

        if (! is_array($this->matches[$key])) {
            return null;
        }

        return $this->matches[$key];
    }

    public function has(): bool
    {
        return $this->count > 0;
    }
}
