<?php

declare(strict_types=1);

namespace DouglasGreen\Utility\FileSystem;

use DouglasGreen\Utility\Exceptions\FileSystem\FileException;

/**
 * The functions in this class depend on a filename not an open file. See File
 * for other file functions.
 */
class Path
{
    public const int USE_INCLUDE_PATH = 1;

    public const int USE_BINARY = 2;

    /**
     * @param ?resource $context
     */
    public function __construct(
        protected string $filename,
        protected int $flags = 0,
        protected $context = null
    ) {}

    /**
     * Substitute for chgrp.
     *
     * @throws FileException
     */
    public function changeGroup(string|int $group): self
    {
        if (chgrp($this->filename, $group) === false) {
            throw new FileException(
                sprintf('Unable to change group of file "%s"', $this->filename),
            );
        }

        return $this;
    }

    /**
     * Substitute for chmod.
     *
     * @throws FileException
     */
    public function changeMode(int $permissions): self
    {
        if (chmod($this->filename, $permissions) === false) {
            throw new FileException(
                sprintf('Unable to change mode of file "%s"', $this->filename),
            );
        }

        return $this;
    }

    /**
     * Substitute for chown.
     *
     * @throws FileException
     */
    public function changeOwner(string|int $user): self
    {
        if (chown($this->filename, $user) === false) {
            throw new FileException(
                sprintf('Unable to change owner of file "%s"', $this->filename),
            );
        }

        return $this;
    }

    /**
     * Substitute for copy.
     *
     * @throws FileException
     */
    public function copy(string $source, string $target): self
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

        return $this;
    }

    /**
     * Substitute for unlink.
     *
     * @throws FileException
     */
    public function delete(): self
    {
        if (unlink($this->filename, $this->context) === false) {
            throw new FileException('Unable to delete file');
        }

        return $this;
    }

    /**
     * Substitute for glob. The "filename" property should be a file pattern.
     *
     * @return list<string>
     * @throws FileException
     */
    public function findAll(): array
    {
        $result = glob($this->filename, $this->flags);
        if ($result === false) {
            throw new FileException(
                sprintf(
                    'Unable to search files for pattern "%s"',
                    $this->filename
                ),
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
     * Substitute for fileperms.
     *
     * @throws FileException
     */
    public function getPermissions(): int
    {
        $result = fileperms($this->filename);
        if ($result === false) {
            throw new FileException(
                sprintf(
                    'Unable to get permissions of file "%s"',
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
    public function loadAndPrint(): int
    {
        $useIncludePath = (bool) ($this->flags & self::USE_INCLUDE_PATH);
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
    public function loadLines(): array
    {
        $result = file($this->filename, $this->flags, $this->context);

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
        int $offset = 0,
        ?int $length = null,
    ): string {
        $useIncludePath = (bool) ($this->flags & self::USE_INCLUDE_PATH);
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
     * Substitute for md5_file.
     *
     * @throws FileException
     */
    public function md5(): string
    {
        $useBinary = (bool) ($this->flags & self::USE_BINARY);
        $result = md5_file($this->filename, $useBinary);
        if ($result === false) {
            throw new FileException(
                sprintf(
                    'Unable to calculate MD5 hash of file "%s"',
                    $this->filename,
                ),
            );
        }

        return $result;
    }

    /**
     * Substitute for rename.
     *
     * @throws FileException
     */
    public function rename(string $target): self
    {
        if (rename($this->filename, $target, $this->context) === false) {
            throw new FileException(
                sprintf(
                    'Unable to rename file from "%s" to "%s"',
                    $this->filename,
                    $target,
                ),
            );
        }

        // Update filename to new name.
        $this->filename = $target;

        return $this;
    }

    /**
     * Substitute for realpath.
     *
     * @throws FileException
     */
    public function resolve(): string
    {
        $result = realpath($this->filename);
        if ($result === false) {
            throw new FileException(
                sprintf('Unable to get real path on "%s"', $this->filename),
            );
        }

        return $result;
    }

    /**
     * Substitute for file_put_contents.
     *
     * @throws FileException
     */
    public function saveString(mixed $data): int
    {
        $result = file_put_contents(
            $this->filename,
            $data,
            $this->flags,
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
     * Substitute for symlink. The "filename" property should be the target.
     *
     * @throws FileException
     */
    public function symlink(string $link): self
    {
        if (symlink($this->filename, $link) === false) {
            throw new FileException(
                sprintf(
                    'Unable to link "%s" to file "%s"',
                    $link,
                    $this->filename
                ),
            );
        }

        return $this;
    }

    /**
     * Substitute for touch.
     *
     * @throws FileException
     */
    public function touch(?int $mtime = null, ?int $atime = null): self
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

        return $this;
    }
}
