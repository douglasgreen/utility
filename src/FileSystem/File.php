<?php

declare(strict_types=1);

namespace DouglasGreen\Utility\FileSystem;

use DouglasGreen\Utility\Exceptions\FileSystem\FileException;

/**
 * File utility class to throw exceptions when basic operations fail.
 *
 * The functions in this class depend on an open file. See Path for other
 * file functions.
 *
 * @todo Add other file functions from this list.
 * umask
 * fileperms
 * link
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
 */
class File
{
    public const int USE_INCLUDE_PATH = 1;

    /**
     * @var resource
     */
    protected $stream;

    /**
     * @param ?resource $context
     */
    public function __construct(
        protected string $filename,
        protected string $mode = 'r',
        protected int $flags = 0,
        protected $context = null
    ) {
        $this->open();
    }

    public function __destruct()
    {
        $this->close();
    }

    /**
     * Substitute for fgetcsv.
     *
     * @param ?int<0, max> $length
     * @return ?list<string>
     * @throws FileException
     */
    public function getFields(
        ?int $length = null,
        string $separator = ',',
        string $enclosure = '"',
        string $escape = '\\',
    ): ?array {
        $fields = fgetcsv(
            $this->stream,
            $length,
            $separator,
            $enclosure,
            $escape
        );

        // Distinguish between end-of-data false and error false.
        if ($fields === false) {
            if (! feof($this->stream)) {
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
     * @throws FileException
     */
    public function getLine(?int $length = null): ?string
    {
        $buffer = fgets($this->stream, $length);

        // Distinguish between end-of-data false and error false.
        if ($buffer === false) {
            if (! feof($this->stream)) {
                throw new FileException('Unable to get line from file');
            }

            return null;
        }

        return $buffer;
    }

    /**
     * Substitute for fputcsv.
     *
     * @param list<string|float|int|null> $fields
     * @throws FileException
     */
    public function putFields(
        array $fields,
        string $separator = ',',
        string $enclosure = '"',
        string $escape = '\\',
        string $eol = PHP_EOL
    ): int {
        $result = fputcsv(
            $this->stream,
            $fields,
            $separator,
            $enclosure,
            $escape,
            $eol
        );

        if ($result === false) {
            throw new FileException('Unable to put CSV line into file');
        }

        return $result;
    }

    /**
     * Substitute for fread.
     *
     * @param int<1, max> $length
     * @throws FileException
     */
    public function read(int $length): string
    {
        $result = fread($this->stream, $length);
        if ($result === false) {
            throw new FileException('Unable to read string from file');
        }

        return $result;
    }

    /**
     * Substitute for fpassthru. Assumes that 0 bytes read means failure.
     *
     * @throws FileException
     */
    public function readAndPrintRest(): int
    {
        $result = fpassthru($this->stream);
        if ($result === 0) {
            throw new FileException('Unable to read and print rest of file');
        }

        return $result;
    }

    /**
     * Substitute for rewind.
     *
     * @throws FileException
     */
    public function rewind(): self
    {
        $result = rewind($this->stream);
        if ($result === false) {
            throw new FileException('Unable to rewind file');
        }

        return $this;
    }

    /**
     * Substitute for fseek.
     *
     * @throws FileException
     */
    public function seek(int $offset, int $whence = SEEK_SET): self
    {
        if (fseek($this->stream, $offset, $whence) === -1) {
            throw new FileException('Unable to seek on file');
        }

        return $this;
    }

    /**
     * Substitute for fstat.
     *
     * @return array<string|int, int>
     * @throws FileException
     */
    public function stats(): array
    {
        $result = fstat($this->stream);
        if ($result === false) {
            throw new FileException('Unable to get stats from file');
        }

        return $result;
    }

    /**
     * Substitute for ftell.
     *
     * @throws FileException
     */
    public function tell(): int
    {
        $result = ftell($this->stream);
        if ($result === false) {
            throw new FileException('Unable to tell file');
        }

        return $result;
    }

    /**
     * Substitute for fwrite and its alias fputs.
     *
     * @param ?int<0, max> $length
     * @throws FileException
     */
    public function write(string $data, ?int $length = null): int
    {
        $result = fwrite($this->stream, $data, $length);
        if ($result === false) {
            throw new FileException('Unable to write string to file');
        }

        return $result;
    }

    /**
     * Substitute for fclose.
     *
     * @throws FileException
     */
    protected function close(): void
    {
        if (fclose($this->stream) === false) {
            throw new FileException('Unable to close file');
        }
    }

    /**
     * Substitute for fopen.
     *
     * @throws FileException
     */
    protected function open(): void
    {
        $useIncludePath = (bool) ($this->flags & self::USE_INCLUDE_PATH);
        $stream = fopen(
            $this->filename,
            $this->mode,
            $useIncludePath,
            $this->context
        );
        if ($stream === false) {
            throw new FileException(
                sprintf('Unable to open file "%s"', $this->filename),
            );
        }

        $this->stream = $stream;
    }
}
