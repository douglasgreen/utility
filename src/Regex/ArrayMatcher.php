<?php

declare(strict_types=1);

namespace DouglasGreen\Utility\Regex;

/**
 * Regex utility class to handle preg_replace_callback_array.
 */
class ArrayMatcher extends AbstractMatcher
{
    /**
     * @param array<string, callable> $patterns
     */
    public function __construct(
        protected readonly array $patterns
    ) {}

    /**
     * Substitute for preg_replace_callback_array.
     *
     * Takes a string as $subject so it returns a string.
     *
     * @throws RegexException
     */
    public function replaceCallMap(string $subject, int $limit = -1): string
    {
        $result = preg_replace_callback_array($this->patterns, $subject, $limit, $this->count);

        if ($result === null) {
            throw new RegexException($this->getErrorMessage());
        }

        return $result;
    }

    /**
     * Substitute for preg_replace_callback_array.
     *
     * Takes a list<string> as $subject so it returns a list<string>.
     *
     * @param list<string> $subject
     * @return list<string>
     * @throws RegexException
     */
    public function replaceCallMapList(array $subject, int $limit = -1): array
    {
        $result = preg_replace_callback_array($this->patterns, $subject, $limit, $this->count);

        // On error, empty array is returned, so I check preg_last_error() instead.
        if ($result === null || preg_last_error() !== PREG_NO_ERROR) {
            throw new RegexException($this->getErrorMessage());
        }

        return $result;
    }

    protected function getPatternDescription(): string
    {
        return implode(', ', array_keys($this->patterns));
    }
}
