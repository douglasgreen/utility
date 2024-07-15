<?php

declare(strict_types=1);

namespace DouglasGreen\Utility\Regex;

/**
 * Base class to hold information about flags and matches.
 */
abstract class AbstractMatcher
{
    protected int $count = 0;

    public function getCount(): int
    {
        return $this->count;
    }

    public function matched(): bool
    {
        return $this->count > 0;
    }

    protected function getErrorMessage(): string
    {
        $errorMessage = 'Regex failed';
        if (preg_last_error() !== PREG_NO_ERROR) {
            $errorMessage .=
                ' with error "' . preg_last_error_msg() . '": ' . $this->getPatternDescription();
        }

        return $errorMessage;
    }

    abstract protected function getPatternDescription(): string;
}
