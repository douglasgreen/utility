<?php

declare(strict_types=1);

namespace DouglasGreen\Utility;

use DouglasGreen\Utility\Exceptions\Data\ValueException;

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
     * Get one or all matches.
     *
     * Pass an int to get just that numbered match.
     * Pass a string to get just that named match.
     * Pass no argument to get the whole match or all matches.
     * If match is requested but not set, return null.
     *
     * @return array<int, mixed>|string|null
     */
    public function get(string|int|null $key = null): array|string|null
    {
        if ($key === null) {
            return $this->matches;
        }

        return $this->matches[$key] ?? null;
    }

    /**
     * Like get, but only return an array.
     *
     * @return array<int, mixed>
     */
    public function getArray(string|int|null $key = null): array
    {
        $result = $this->get($key);
        if (! is_array($result)) {
            throw new ValueException('Wrong type returned: ' . gettype(
                $result
            ));
        }

        return $result;
    }

    /**
     * Like get, but only return a string.
     */
    public function getString(string|int|null $key = null): string
    {
        $result = $this->get($key);
        if (! is_string($result)) {
            throw new ValueException('Wrong type returned: ' . gettype(
                $result
            ));
        }

        return $result;
    }

    public function has(): bool
    {
        return $this->count > 0;
    }
}
