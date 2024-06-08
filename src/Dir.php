<?php

declare(strict_types=1);

namespace DouglasGreen\Utility;

use DouglasGreen\Utility\Exceptions\FileSystem\DirectoryException;

/**
 * Directory utility class to throw exceptions when basic operations fail.
 */
class Dir
{
    /**
     * Substitute for getcwd.
     *
     * @throws DirectoryException
     */
    public static function getCurrent(): string
    {
        $result = getcwd();
        if ($result === false) {
            throw new DirectoryException(
                'Unable to get current working directory',
            );
        }

        return $result;
    }

    /**
     * Substitute for mkdir.
     *
     * @param ?resource $context
     * @throws DirectoryException
     */
    public static function make(
        string $directory,
        int $permissions = 0o777,
        bool $recursive = false,
        $context = null
    ): void {
        if (mkdir($directory, $permissions, $recursive, $context) === false) {
            throw new DirectoryException('Unable to make directory');
        }
    }

    /**
     * Substitute for rmdir.
     *
     * @param ?resource $context
     * @throws DirectoryException
     */
    public static function remove(string $directory, $context = null): void
    {
        if (rmdir($directory, $context) === false) {
            throw new DirectoryException('Unable to make directory');
        }
    }
}
