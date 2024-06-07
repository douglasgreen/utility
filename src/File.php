<?php

declare(strict_types=1);

namespace DouglasGreen\Utility;

use DouglasGreen\Utility\Exceptions\FileSystem\FileException;

/**
 * File utility class to throw exceptions when basic operations fail.
 *
 * @todo Add other file functions from this list.
 * rewind
 * unlink
 * fwrite
 * file
 * touch
 * mkdir
 * fread
 * fseek
 * copy
 * chmod
 * tempnam
 * filesize
 * rename
 * rmdir
 * ftell
 * pathinfo
 * readfile
 * glob
 * symlink
 * fstat
 * filemtime
 * umask
 * fileperms
 * link
 * fputs
 * ftruncate
 * flock
 * readlink
 * chgrp
 * stat
 * chown
 * move_uploaded_file
 * disk_free_space
 * fpassthru
 * popen
 * pclose
 * lstat
 * parse_ini_file
 */
class File
{
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
                throw new FileException('Unable to get line from file');
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
            throw new FileException(sprintf('Unable to open "%s"', $filename));
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
}
