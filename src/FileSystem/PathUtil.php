<?php

declare(strict_types=1);

namespace DouglasGreen\Utility\FileSystem;

/**
 * Path helper class with static functions.
 *
 * These functions call the equivalent functions in Path.
 */
class PathUtil
{
    /* Same consonants as Path class */
    public const APPEND = 1;

    public const IGNORE_NEW_LINES = 2;

    public const LOCK = 4;

    public const SKIP_EMPTY_LINES = 8;

    public const USE_BINARY = 16;

    public const USE_INCLUDE_PATH = 32;

    public static function addSubpath(string $path, string $subpath): string
    {
        $path = new Path($path);
        $path->addSubpath($subpath);
        return $path->getPath();
    }

    public static function calcMd5(string $path, int $flags = 0): string
    {
        $path = new Path($path);
        return $path->calcMd5($flags);
    }

    public static function changeGroup(string $path, string|int $group): Path
    {
        $path = new Path($path);
        $path->changeGroup($group);
        return $path;
    }

    public static function changeMode(string $path, int $permissions): Path
    {
        $path = new Path($path);
        $path->changeMode($permissions);
        return $path;
    }

    public static function changeOwner(string $path, string|int $user): Path
    {
        $path = new Path($path);
        $path->changeOwner($user);
        return $path;
    }

    public static function copy(string $path, string $target): Path
    {
        $path = new Path($path);
        $path->copy($target);
        return $path;
    }

    public static function delete(string $path): Path
    {
        $path = new Path($path);
        $path->delete();
        return $path;
    }

    public static function exists(string $path): bool
    {
        return file_exists($path);
    }

    public static function getAccessTime(string $path): int
    {
        $path = new Path($path);
        return $path->getAccessTime();
    }

    /**
     * @return array<string, int>
     */
    public static function getLinkStats(string $path): array
    {
        $path = new Path($path);
        return $path->getLinkStats();
    }

    public static function getLinkTarget(string $path): string
    {
        $path = new Path($path);
        return $path->getLinkTarget();
    }

    public static function getMetaChangeTime(string $path): int
    {
        $path = new Path($path);
        return $path->getMetaChangeTime();
    }

    public static function getPermissions(string $path): int
    {
        $path = new Path($path);
        return $path->getPermissions();
    }

    public static function getRelativeSubpath(string $path, string $absolutePath): string
    {
        $path = new Path($path);
        return $path->getRelativeSubpath($absolutePath);
    }

    /**
     * @return array<string, int>
     */
    public static function getStats(string $path): array
    {
        $path = new Path($path);
        return $path->getStats();
    }

    public static function getWriteTime(string $path): int
    {
        $path = new Path($path);
        return $path->getWriteTime();
    }

    public static function isDirectory(string $path): bool
    {
        return is_dir($path);
    }

    public static function isExecutable(string $path): bool
    {
        return is_executable($path);
    }

    public static function isFile(string $path): bool
    {
        return is_file($path);
    }

    public static function isReadable(string $path): bool
    {
        return is_readable($path);
    }

    public static function isSame(string $path, string $other): bool
    {
        $path = new Path($path);
        return $path->isSame($other);
    }

    public static function isUpload(string $path): bool
    {
        return is_uploaded_file($path);
    }

    public static function isWritable(string $path): bool
    {
        return is_writable($path);
    }

    public static function loadAndPrint(string $path, int $flags = 0): int
    {
        $path = new Path($path);
        return $path->loadAndPrint($flags);
    }

    /**
     * @return list<string>
     */
    public static function loadLines(string $path, int $flags = 0): array
    {
        $path = new Path($path);
        return $path->loadLines($flags);
    }

    /**
     * @param ?int<0, max> $length
     */
    public static function loadString(
        string $path,
        int $offset = 0,
        int $flags = 0,
        ?int $length = null,
    ): string {
        $path = new Path($path);
        return $path->loadString($offset, $flags, $length);
    }

    public static function makeDirectory(
        string $path,
        int $permissions = 0o777,
        int $flags = 0,
    ): Path {
        $path = new Path($path);
        $path->makeDirectory($permissions, $flags);
        return $path;
    }

    public static function makeHardLink(string $path, string $link): Path
    {
        $path = new Path($path);
        $path->makeHardLink($link);
        return $path;
    }

    public static function makeSymlink(string $path, string $link): Path
    {
        $path = new Path($path);
        $path->makeSymlink($link);
        return $path;
    }

    public static function moveUpload(string $path, string $target): Path
    {
        $path = new Path($path);
        $path->moveUpload($target);
        return $path;
    }

    public static function mustExist(string $path): Path
    {
        $path = new Path($path);
        $path->mustExist();
        return $path;
    }

    public static function rename(string $path, string $target): Path
    {
        $path = new Path($path);
        $path->rename($target);
        return $path;
    }

    public static function resolve(string $path): string
    {
        $path = new Path($path);
        return $path->resolve();
    }

    public static function saveString(string $path, mixed $data, int $flags = 0): int
    {
        $path = new Path($path);
        return $path->saveString($data, $flags);
    }

    public static function size(string $path): int
    {
        $path = new Path($path);
        return $path->size();
    }

    public static function touch(string $path, ?int $mtime = null, ?int $atime = null): Path
    {
        $path = new Path($path);
        $path->touch($mtime, $atime);
        return $path;
    }
}
