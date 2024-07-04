<?php

declare(strict_types=1);

namespace DouglasGreen\Utility\Regex;

use DouglasGreen\Utility\Data\FlagChecker;
use DouglasGreen\Utility\Data\FlagHandler;

/**
 * Base class to hold information about flags and matches.
 */
abstract class AbstractMatcher implements FlagHandler
{
    public const DELIM_CAPTURE = 1;

    public const NO_EMPTY = 2;

    protected int $count = 0;

    public static function getFlagChecker(int $flags): FlagChecker
    {
        $flagNames = [
            'delimCapture' => self::DELIM_CAPTURE,
            'noEmpty' => self::NO_EMPTY,
        ];
        return new FlagChecker($flagNames, $flags);
    }

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
