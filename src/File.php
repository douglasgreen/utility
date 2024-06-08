<?php

declare(strict_types=1);

namespace DouglasGreen\Utility;

use DouglasGreen\Utility\Exceptions\FileSystem\FileException;

/**
 * File utility class to throw exceptions when basic operations fail.
 *
 * @todo Add other file functions from this list.
 * umask
 * fileperms
 * link
 * fputs
 * ftruncate
 * flock
 * readlink
 * stat
 * move_uploaded_file
 * disk_free_space
 * popen
 * pclose
 * lstat
 * parse_ini_file
 *
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class File
{
    /**
     * Substitute for chgrp.
     *
     * @throws FileException
     */
    public static function changeGroup(
        string $filename,
        string|int $group,
    ): void {
        if (chgrp($filename, $group) === false) {
            throw new FileException(
                sprintf('Unable to change group of file "%s"', $filename),
            );
        }
    }

    /**
     * Substitute for chmod.
     *
     * @throws FileException
     */
    public static function changeMode(string $filename, int $permissions): void
    {
        if (chmod($filename, $permissions) === false) {
            throw new FileException(
                sprintf('Unable to change mode of file "%s"', $filename),
            );
        }
    }

    /**
     * Substitute for chown.
     *
     * @throws FileException
     */
    public static function changeOwner(string $filename, string|int $user): void
    {
        if (chown($filename, $user) === false) {
            throw new FileException(
                sprintf('Unable to change owner of file "%s"', $filename),
            );
        }
    }

    /**
     * Substitute for fclose.
     *
     * @param resource $stream
     * @throws FileException
     */
    public static function close($stream): void
    {
        if (fclose($stream) === false) {
            throw new FileException('Unable to close file');
        }
    }

    /**
     * Substitute for copy.
     *
     * @param ?resource $context
     * @throws FileException
     */
    public static function copy(string $source, string $target, $context): void
    {
        if (copy($source, $target, $context) === false) {
            throw new FileException(
                sprintf(
                    'Unable to copy file from "%s" to "%s"',
                    $source,
                    $target,
                ),
            );
        }
    }

    /**
     * Substitute for unlink.
     *
     * @param ?resource $context
     * @throws FileException
     */
    public static function delete(string $filename, $context): void
    {
        if (unlink($filename, $context) === false) {
            throw new FileException('Unable to delete file');
        }
    }

    /**
     * Substitute for filesize.
     *
     * @throws FileException
     */
    public static function size(string $filename): int
    {
        $result = filesize($filename);
        if ($result === false) {
            throw new FileException(
                sprintf('Unable to get size of file "%s"', $filename),
            );
        }

        return $result;
    }

    /**
     * Substitute for fileatime.
     *
     * @throws FileException
     */
    public static function getAccessTime(string $filename): int
    {
        $result = fileatime($filename);
        if ($result === false) {
            throw new FileException(
                sprintf(
                    'Unable to get last access time of file "%s"',
                    $filename,
                ),
            );
        }

        return $result;
    }

    /**
     * Substitute for fgetcsv.
     *
     * @param ?int<0, max> $length
     * @param resource $stream
     * @return ?list<string>
     * @throws FileException
     */
    public static function getCsv(
        $stream,
        ?int $length = null,
        string $separator = ',',
        string $enclosure = '"',
        string $escape = '\\',
    ): ?array {
        $fields = fgetcsv($stream, $length, $separator, $enclosure, $escape);

        // Distinguish between end-of-data false and error false.
        if ($fields === false) {
            if (! feof($stream)) {
                throw new FileException('Unable to get CSV line from file');
            }

            return null;
        }

        return $fields;
    }

    /**
     * Substitute for fgets.
     *
     * @param ?int<0, max> $length
     * @param resource $stream
     * @throws FileException
     */
    public static function getLine($stream, ?int $length = null): ?string
    {
        $buffer = fgets($stream, $length);

        // Distinguish between end-of-data false and error false.
        if ($buffer === false) {
            if (! feof($stream)) {
                throw new FileException('Unable to get line from file');
            }

            return null;
        }

        return $buffer;
    }

    /**
     * Substitute for filectime.
     *
     * @throws FileException
     */
    public static function getMetaChangeTime(string $filename): int
    {
        $result = filectime($filename);
        if ($result === false) {
            throw new FileException(
                sprintf(
                    'Unable to get last metadata change time of file "%s"',
                    $filename,
                ),
            );
        }

        return $result;
    }

    /**
     * Substitute for filemtime.
     *
     * @throws FileException
     */
    public static function getWriteTime(string $filename): int
    {
        $result = filemtime($filename);
        if ($result === false) {
            throw new FileException(
                sprintf(
                    'Unable to get last modification time of file "%s"',
                    $filename,
                ),
            );
        }

        return $result;
    }

    /**
     * Substitute for readfile.
     *
     * @param ?resource $context
     * @throws FileException
     */
    public static function loadAndPrint(
        string $filename,
        bool $useIncludePath = false,
        $context = null,
    ): int {
        $result = readfile($filename, $useIncludePath, $context);
        if ($result === false) {
            throw new FileException(
                sprintf('Unable to load and print file "%s"', $filename),
            );
        }

        return $result;
    }

    /**
     * Substitute for file.
     *
     * @param ?resource $context
     * @return list<string>
     */
    public static function loadLines(
        string $filename,
        int $flags = 0,
        $context = null,
    ): array {
        $result = file($filename, $flags, $context);

        if ($result === false) {
            throw new FileException('Unable to load file to array');
        }

        return $result;
    }

    /**
     * Substitute for file_get_contents.
     *
     * @param resource $context
     * @param ?int<0, max> $length
     * @throws FileException
     */
    public static function loadString(
        string $filename,
        bool $useIncludePath = false,
        $context = null,
        int $offset = 0,
        ?int $length = null,
    ): string {
        $result = file_get_contents(
            $filename,
            $useIncludePath,
            $context,
            $offset,
            $length,
        );

        if ($result === false) {
            throw new FileException('Unable to load file to string');
        }

        return $result;
    }

    /**
     * Substitute for tempnam.
     *
     * @throws FileException
     */
    public static function makeTemp(string $directory, string $prefix): string
    {
        $result = tempnam($directory, $prefix);
        if ($result === false) {
            throw new FileException(
                sprintf(
                    'Unable to create temp file in directory "%s"',
                    $directory,
                ),
            );
        }

        return $result;
    }

    /**
     * Substitute for glob.
     *
     * @return list<string>
     * @throws FileException
     */
    public static function findAll(string $pattern, int $flags = 0): array
    {
        $result = glob($pattern, $flags);
        if ($result === false) {
            throw new FileException(
                sprintf('Unable to search files for pattern "%s"', $pattern),
            );
        }

        return $result;
    }

    /**
     * Substitute for fopen.
     *
     * @param resource $context
     * @return resource
     * @throws FileException
     */
    public static function open(
        string $filename,
        string $mode,
        bool $useIncludePath = false,
        $context = null,
    ) {
        $handle = fopen($filename, $mode, $useIncludePath, $context);
        if ($handle === false) {
            throw new FileException(
                sprintf('Unable to open file "%s"', $filename),
            );
        }

        return $handle;
    }

    /**
     * Substitute for realpath.
     *
     * @throws FileException
     */
    public static function path(string $path): string
    {
        $result = realpath($path);
        if ($result === false) {
            throw new FileException(
                sprintf('Unable to get real path on "%s"', $path),
            );
        }

        return $result;
    }

    /**
     * Substitute for fread.
     *
     * @param resource $stream
     * @param int<1, max> $length
     * @throws FileException
     */
    public static function read($stream, int $length): string
    {
        $result = fread($stream, $length);
        if ($result === false) {
            throw new FileException('Unable to read string from file');
        }

        return $result;
    }

    /**
     * Substitute for fpassthru. Assumes that 0 bytes read means failure.
     *
     * @param resource $stream
     * @throws FileException
     */
    public static function readAndPrintRest($stream): int
    {
        $result = fpassthru($stream);
        if ($result === 0) {
            throw new FileException('Unable to read and print rest of file');
        }

        return $result;
    }

    /**
     * Substitute for rename.
     *
     * @param ?resource $context
     * @throws FileException
     */
    public static function rename(
        string $source,
        string $target,
        $context = null,
    ): void {
        if (rename($source, $target, $context) === false) {
            throw new FileException(
                sprintf(
                    'Unable to rename file from "%s" to "%s"',
                    $source,
                    $target,
                ),
            );
        }
    }

    /**
     * Substitute for rewind.
     *
     * @param resource $stream
     * @throws FileException
     */
    public static function rewind($stream): void
    {
        $result = rewind($stream);
        if ($result === false) {
            throw new FileException('Unable to rewind file');
        }
    }

    /**
     * Substitute for file_put_contents.
     *
     * @param resource $context
     * @throws FileException
     */
    public static function saveString(
        string $filename,
        mixed $data,
        int $flags = 0,
        $context = null,
    ): int {
        $result = file_put_contents($filename, $data, $flags, $context);
        if ($result === false) {
            throw new FileException('Unable to save string to file');
        }

        return $result;
    }

    /**
     * Substitute for fseek.
     *
     * @param resource $stream
     * @throws FileException
     */
    public static function seek(
        $stream,
        int $offset,
        int $whence = SEEK_SET,
    ): void {
        if (fseek($stream, $offset, $whence) === -1) {
            throw new FileException('Unable to seek on file');
        }
    }

    /**
     * Substitute for fstat.
     *
     * @param resource $stream
     * @return array<string|int, int>
     * @throws FileException
     */
    public static function stats($stream): array
    {
        $result = fstat($stream);
        if ($result === false) {
            throw new FileException('Unable to get stats from file');
        }

        return $result;
    }

    /**
     * Substitute for symlink.
     *
     * @throws FileException
     */
    public static function symlink(string $target, string $link): void
    {
        if (symlink($target, $link) === false) {
            throw new FileException(
                sprintf('Unable to link "%s" to file "%s"', $link, $target),
            );
        }
    }

    /**
     * Substitute for ftell.
     *
     * @param resource $stream
     * @throws FileException
     */
    public static function tell($stream): int
    {
        $result = ftell($stream);
        if ($result === false) {
            throw new FileException('Unable to tell file');
        }

        return $result;
    }

    /**
     * Substitute for touch.
     *
     * @throws FileException
     */
    public static function touch(
        string $filename,
        ?int $mtime = null,
        ?int $atime = null,
    ): void {
        $result = touch($filename, $mtime, $atime);
        if ($result === false) {
            throw new FileException(
                sprintf(
                    'Unable set file access and modification times on file "%s"',
                    $filename,
                ),
            );
        }
    }

    /**
     * Substitute for fwrite.
     *
     * @param resource $stream
     * @param ?int<0, max> $length
     * @throws FileException
     */
    public static function write(
        $stream,
        string $data,
        ?int $length = null,
    ): int {
        $result = fwrite($stream, $data, $length);
        if ($result === false) {
            throw new FileException('Unable to write string to file');
        }

        return $result;
    }
}
