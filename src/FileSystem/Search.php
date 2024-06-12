<?php

declare(strict_types=1);

namespace DouglasGreen\Utility\FileSystem;

use DouglasGreen\Utility\Exceptions\FileSystem\FileException;

/**
 * The function in this class depends on a search pattern.
 */
class Search
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
        $phpFlags = 0;
        if (($flags & self::ADD_SLASH) !== 0) {
            $phpFlags |= GLOB_MARK;
        }

        if (($flags & self::NO_ESCAPE) !== 0) {
            $phpFlags |= GLOB_NOESCAPE;
        }

        if (($flags & self::NO_SORT) !== 0) {
            $phpFlags |= GLOB_NOSORT;
        }

        if (($flags & self::ONLY_DIRS) !== 0) {
            $phpFlags |= GLOB_ONLYDIR;
        }

        if (($flags & self::STOP_ON_ERROR) !== 0) {
            $phpFlags |= GLOB_ERR;
        }

        $result = glob($pattern, $phpFlags);
        if ($result === false) {
            throw new FileException(
                sprintf(
                    'Unable to search files for pattern "%s"',
                    $pattern
                ),
            );
        }

        return $result;
    }
}
