<?php

declare(strict_types=1);

namespace DouglasGreen\Utility\FileSystem;

use DouglasGreen\Utility\Data\FlagChecker;
use DouglasGreen\Utility\Data\FlagHandler;

/**
 * The function in this class depends on a search pattern.
 */
class Search implements FlagHandler
{
    public const ADD_SLASH = 1;

    public const NO_ESCAPE = 2;

    public const NO_SORT = 4;

    public const ONLY_DIRS = 8;

    public const STOP_ON_ERROR = 16;

    /**
     * Substitute for glob.
     *
     * @return list<string>
     * @throws FileException
     */
    public static function findAll(string $pattern, int $flags = 0): array
    {
        $flagChecker = static::getFlagChecker($flags);
        $phpFlags = 0;
        if ($flagChecker->get('addSlash')) {
            $phpFlags |= GLOB_MARK;
        }

        if ($flagChecker->get('noEscape')) {
            $phpFlags |= GLOB_NOESCAPE;
        }

        if ($flagChecker->get('noSort')) {
            $phpFlags |= GLOB_NOSORT;
        }

        if ($flagChecker->get('onlyDirs')) {
            $phpFlags |= GLOB_ONLYDIR;
        }

        if ($flagChecker->get('stopOnError')) {
            $phpFlags |= GLOB_ERR;
        }

        $result = glob($pattern, $phpFlags);
        if ($result === false) {
            throw new FileException(sprintf('Unable to search files for pattern "%s"', $pattern));
        }

        return $result;
    }

    public static function getFlagChecker(int $flags): FlagChecker
    {
        $flagNames = [
            'addSlash' => self::ADD_SLASH,
            'noEscape' => self::NO_ESCAPE,
            'noSort' => self::NO_SORT,
            'onlyDirs' => self::ONLY_DIRS,
            'stopOnError' => self::STOP_ON_ERROR,
        ];
        return new FlagChecker($flagNames, $flags);
    }
}
