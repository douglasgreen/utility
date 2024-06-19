<?php

declare(strict_types=1);

namespace DouglasGreen\Utility\FileSystem;

use Directory as PhpDirectory;

/**
 * Directory helper class with static functions.
 *
 * These functions call the equivalent functions in Directory.
 */
class DirUtil
{
    /**
     * @var int
     */
    public const NO_DOT_DIRS = 1;

    /**
     * @var int
     */
    public const RECURSIVE = 2;

    /**
     * @var int
     */
    public const SORT_ASCENDING = 4;

    /**
     * @var int
     */
    public const SORT_DESCENDING = 8;

    /**
     * @var int
     */
    public const SORT_NONE = 16;

    /**
     * @throws DirectoryException
     */
    public static function getCurrent(): string
    {
        $result = getcwd();
        if ($result === false) {
            throw new DirectoryException('Unable to get current working directory');
        }

        return $result;
    }

    /**
     * @return list<string>
     */
    public static function listFiles(string $path, int $flags = 0): array
    {
        $directory = new Directory($path);
        return $directory->listFiles($flags);
    }

    public static function make(string $path, int $permissions = 0o777, int $flags = 0): Directory
    {
        $directory = new Directory($path);
        $directory->make($permissions, $flags);
        return $directory;
    }

    public static function makeTemp(string $path, string $prefix): string
    {
        $directory = new Directory($path);
        return $directory->makeTemp($prefix);
    }

    public static function open(string $path): PhpDirectory
    {
        $directory = new Directory($path);
        return $directory->open();
    }

    public static function remove(string $path, int $flags = 0): void
    {
        $directory = new Directory($path);
        $directory->remove($flags);
    }

    public static function removeContents(string $path): void
    {
        $directory = new Directory($path);
        $directory->removeContents();
    }

    public static function removeRecursive(string $path): void
    {
        $directory = new Directory($path);
        $directory->removeRecursive();
    }

    /**
     * @return list<string>
     */
    public static function scan(string $path, int $flags = 0): array
    {
        $directory = new Directory($path);
        return $directory->scan($flags);
    }

    public static function setCurrent(string $path): Directory
    {
        $directory = new Directory($path);
        $directory->setCurrent();
        return $directory;
    }
}
