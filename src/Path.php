<?php

declare(strict_types=1);

namespace DouglasGreen\Utility;

use DouglasGreen\Utility\Exceptions\FileSystem\FileException;

/**
 * The functions in this class depend on a filename not an open file. See File
 * for other file functions.
 *
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class Path
{
    /**
     * @var ?resource
     */
    protected $stream;

    /**
     * @param ?resource $context
     */
    public function __construct(
        protected string $filename,
        protected $context = null
    ) {}

    /**
     * Substitute for chgrp.
     *
     * @throws FileException
     */
    public function changeGroup(string|int $group): void
    {
        if (chgrp($this->filename, $group) === false) {
            throw new FileException(
                sprintf('Unable to change group of file "%s"', $this->filename),
            );
        }
    }

    /**
     * Substitute for chmod.
     *
     * @throws FileException
     */
    public function changeMode(int $permissions): void
    {
        if (chmod($this->filename, $permissions) === false) {
            throw new FileException(
                sprintf('Unable to change mode of file "%s"', $this->filename),
            );
        }
    }

    /**
     * Substitute for chown.
     *
     * @throws FileException
     */
    public function changeOwner(string|int $user): void
    {
        if (chown($this->filename, $user) === false) {
            throw new FileException(
                sprintf('Unable to change owner of file "%s"', $this->filename),
            );
        }
    }

    /**
     * Substitute for copy.
     *
     * @throws FileException
     */
    public function copy(string $source, string $target): void
    {
        if (copy($source, $target, $this->context) === false) {
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
     * @throws FileException
     */
    public function delete(): void
    {
        if (unlink($this->filename, $this->context) === false) {
            throw new FileException('Unable to delete file');
        }
    }

    /**
     * Substitute for glob.
     *
     * @return list<string>
     * @throws FileException
     */
    public function findAll(string $pattern, int $flags = 0): array
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
     * Substitute for fileatime.
     *
     * @throws FileException
     */
    public function getAccessTime(): int
    {
        $result = fileatime($this->filename);
        if ($result === false) {
            throw new FileException(
                sprintf(
                    'Unable to get last access time of file "%s"',
                    $this->filename,
                ),
            );
        }

        return $result;
    }

    /**
     * Substitute for filectime.
     *
     * @throws FileException
     */
    public function getMetaChangeTime(): int
    {
        $result = filectime($this->filename);
        if ($result === false) {
            throw new FileException(
                sprintf(
                    'Unable to get last metadata change time of file "%s"',
                    $this->filename,
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
    public function getWriteTime(): int
    {
        $result = filemtime($this->filename);
        if ($result === false) {
            throw new FileException(
                sprintf(
                    'Unable to get last modification time of file "%s"',
                    $this->filename,
                ),
            );
        }

        return $result;
    }

    /**
     * Substitute for readfile.
     *
     * @throws FileException
     */
    public function loadAndPrint(bool $useIncludePath = false): int
    {
        $result = readfile($this->filename, $useIncludePath, $this->context);
        if ($result === false) {
            throw new FileException(
                sprintf('Unable to load and print file "%s"', $this->filename),
            );
        }

        return $result;
    }

    /**
     * Substitute for file.
     *
     * @return list<string>
     */
    public function loadLines(int $flags = 0): array
    {
        $result = file($this->filename, $flags, $this->context);

        if ($result === false) {
            throw new FileException('Unable to load file to array');
        }

        return $result;
    }

    /**
     * Substitute for file_get_contents.
     *
     * @param ?int<0, max> $length
     * @throws FileException
     */
    public function loadString(
        bool $useIncludePath = false,
        int $offset = 0,
        ?int $length = null,
    ): string {
        $result = file_get_contents(
            $this->filename,
            $useIncludePath,
            $this->context,
            $offset,
            $length,
        );

        if ($result === false) {
            throw new FileException('Unable to load file to string');
        }

        return $result;
    }

    /**
     * Substitute for rename.
     *
     * @throws FileException
     */
    public function rename(string $source, string $target): void
    {
        if (rename($source, $target, $this->context) === false) {
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
     * Substitute for realpath.
     *
     * @throws FileException
     */
    public function resolve(string $path): string
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
     * @throws FileException
     */
    public function saveString(mixed $data, int $flags = 0): int
    {
        $result = file_put_contents(
            $this->filename,
            $data,
            $flags,
            $this->context
        );
        if ($result === false) {
            throw new FileException('Unable to save string to file');
        }

        return $result;
    }

    /**
     * Substitute for filesize.
     *
     * @throws FileException
     */
    public function size(): int
    {
        $result = filesize($this->filename);
        if ($result === false) {
            throw new FileException(
                sprintf('Unable to get size of file "%s"', $this->filename),
            );
        }

        return $result;
    }

    /**
     * Substitute for symlink.
     *
     * @throws FileException
     */
    public function symlink(string $target, string $link): void
    {
        if (symlink($target, $link) === false) {
            throw new FileException(
                sprintf('Unable to link "%s" to file "%s"', $link, $target),
            );
        }
    }

    /**
     * Substitute for touch.
     *
     * @throws FileException
     */
    public function touch(?int $mtime = null, ?int $atime = null): void
    {
        $result = touch($this->filename, $mtime, $atime);
        if ($result === false) {
            throw new FileException(
                sprintf(
                    'Unable set file access and modification times on file "%s"',
                    $this->filename,
                ),
            );
        }
    }
}
