<?php

declare(strict_types=1);

namespace DouglasGreen\Utility\Data;

/**
 * Flag handler interface for classes the define flags and return a FlagChecker.
 *
 * See FlagChecker for a sample definition.
 */
interface FlagHandler
{
    public static function getFlagChecker(int $flags): FlagChecker;
}
