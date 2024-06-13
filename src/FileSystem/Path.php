<?php

declare(strict_types=1);

namespace DouglasGreen\Utility\FileSystem;

use DouglasGreen\Utility\Exceptions\FileSystem\FileException;
use DouglasGreen\Utility\Url;

/**
 * The functions in this class depend on a file path string, not an open file.
 * See File for other file functions.
 */
class Path
{
    public const APPEND = 1;

    public const IGNORE_NEW_LINES = 2;

    public const LOCK = 4;

    public const SKIP_EMPTY_LINES = 8;

    public const USE_BINARY = 16;

    public const USE_INCLUDE_PATH = 32;

    public static function add(string $path, string $subpath): string
    {
        // Ensure the current filename ends with a directory separator
        if (substr($path, -1) !== DIRECTORY_SEPARATOR) {
            $path .= DIRECTORY_SEPARATOR;
        }

        // Ensure the subpath does not start with a directory separator
        if (substr($subpath, 0, 1) === DIRECTORY_SEPARATOR) {
            $subpath = ltrim($subpath, DIRECTORY_SEPARATOR);
        }

        return $path . $subpath;
    }

    /**
     * @param ?resource $context
     */
    public function __construct(
        protected string $path,
        protected $context = null
    ) {}

    /**
     * Add a subpath to this object.
     */
    public function addSubpath(string $subpath): self
    {
        $this->path = self::add($this->path, $subpath);

        return $this;
    }

    /**
     * Substitute for md5_file.
     *
     * @throws FileException
     */
    public function calcMd5(int $flags = 0): string
    {
        $useBinary = (bool) ($flags & self::USE_BINARY);
        $result = md5_file($this->path, $useBinary);
        if ($result === false) {
            throw new FileException(
                sprintf('Unable to calculate MD5 hash of file "%s"', $this->path),
            );
        }

        return $result;
    }

    /**
     * Substitute for chgrp.
     *
     * @throws FileException
     */
    public function changeGroup(string|int $group): self
    {
        if (chgrp($this->path, $group) === false) {
            throw new FileException(
                sprintf('Unable to change group of file "%s"', $this->path),
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
        if (chmod($this->path, $permissions) === false) {
            throw new FileException(sprintf('Unable to change mode of file "%s"', $this->path));
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
        if (chown($this->path, $user) === false) {
            throw new FileException(
                sprintf('Unable to change owner of file "%s"', $this->path),
            );
        }

        return $this;
    }

    /**
     * Substitute for copy.
     *
     * Returns target path, not self.
     *
     * @throws FileException
     */
    public function copy(string $target): self
    {
        if (copy($this->path, $target, $this->context) === false) {
            throw new FileException(
                sprintf('Unable to copy file from "%s" to "%s"', $this->path, $target),
            );
        }

        return new self($target);
    }

    /**
     * Substitute for unlink.
     *
     * @throws FileException
     */
    public function delete(): self
    {
        if (unlink($this->path, $this->context) === false) {
            throw new FileException('Unable to delete file');
        }

        return $this;
    }

    /**
     * Substitute for file_exists.
     */
    public function exists(): bool
    {
        return file_exists($this->path);
    }

    /**
     * Substitute for fileatime.
     *
     * @throws FileException
     */
    public function getAccessTime(): int
    {
        $result = fileatime($this->path);
        if ($result === false) {
            throw new FileException(
                sprintf('Unable to get last access time of file "%s"', $this->path),
            );
        }

        return $result;
    }

    /**
     * Substitute for lstat.
     *
     * Returns named stats only.
     *
     * @return array<string, int>
     * @throws FileException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getLinkStats(): array
    {
        $result = lstat($this->path);
        if ($result === false) {
            throw new FileException(sprintf('Unable to get stats of link "%s"', $this->path));
        }

        return array_filter(
            $result,
            static fn($value, $key): bool => is_string($key),
            ARRAY_FILTER_USE_BOTH
        );
    }

    /**
     * Substitute for readlink.
     *
     * @throws FileException
     */
    public function getLinkTarget(): string
    {
        $result = readlink($this->path);
        if ($result === false) {
            throw new FileException(
                sprintf('Unable to get target of symbolic link "%s"', $this->path),
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
        $result = filectime($this->path);
        if ($result === false) {
            throw new FileException(
                sprintf('Unable to get last metadata change time of file "%s"', $this->path),
            );
        }

        return $result;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Substitute for fileperms.
     *
     * @throws FileException
     */
    public function getPermissions(): int
    {
        $result = fileperms($this->path);
        if ($result === false) {
            throw new FileException(
                sprintf('Unable to get permissions of file "%s"', $this->path),
            );
        }

        return $result;
    }

    /**
     * Substitute for stat.
     *
     * Returns named stats only.
     *
     * @return array<string, int>
     * @throws FileException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getStats(): array
    {
        $result = stat($this->path);
        if ($result === false) {
            throw new FileException(sprintf('Unable to get stats of file "%s"', $this->path));
        }

        return array_filter(
            $result,
            static fn($value, $key): bool => is_string($key),
            ARRAY_FILTER_USE_BOTH
        );
    }

    /**
     * Substitute for filemtime.
     *
     * @throws FileException
     */
    public function getWriteTime(): int
    {
        $result = filemtime($this->path);
        if ($result === false) {
            throw new FileException(
                sprintf('Unable to get last modification time of file "%s"', $this->path),
            );
        }

        return $result;
    }

    /**
     * Substitute for is_dir.
     */
    public function isDir(): bool
    {
        return is_dir($this->path);
    }

    /**
     * Substitute for is_executable.
     */
    public function isExecutable(): bool
    {
        return is_executable($this->path);
    }

    /**
     * Substitute for is_file.
     */
    public function isFile(): bool
    {
        return is_file($this->path);
    }

    /**
     * Substitute for is_readable.
     */
    public function isReadable(): bool
    {
        return is_readable($this->path);
    }

    /**
     * Substitute for is_uploaded_file.
     */
    public function isUpload(): bool
    {
        return is_uploaded_file($this->path);
    }

    /**
     * Substitute for is_writable.
     */
    public function isWritable(): bool
    {
        return is_writable($this->path);
    }

    /**
     * Substitute for readfile.
     *
     * @throws FileException
     */
    public function loadAndPrint(int $flags = 0): int
    {
        $useIncludePath = (bool) ($flags & self::USE_INCLUDE_PATH);
        $result = readfile($this->path, $useIncludePath, $this->context);
        if ($result === false) {
            throw new FileException(sprintf('Unable to load and print file "%s"', $this->path));
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
        $phpFlags = 0;
        if (($flags & self::IGNORE_NEW_LINES) !== 0) {
            $phpFlags |= FILE_IGNORE_NEW_LINES;
        }

        if (($flags & self::SKIP_EMPTY_LINES) !== 0) {
            $phpFlags |= FILE_SKIP_EMPTY_LINES;
        }

        if (($flags & self::USE_INCLUDE_PATH) !== 0) {
            $phpFlags |= FILE_USE_INCLUDE_PATH;
        }

        $result = file($this->path, $phpFlags, $this->context);

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
    public function loadString(int $offset = 0, int $flags = 0, ?int $length = null): string
    {
        $useIncludePath = (bool) ($flags & self::USE_INCLUDE_PATH);
        $result = file_get_contents(
            $this->path,
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
     * Substitute for file_get_contents on a URL.
     *
     * Automatically encodes the URL if not already encoded.
     */
    public function loadUrl(): ?string
    {
        $url = $this->path;
        if (! Url::isEncoded($url)) {
            $url = urlencode($url);
        }

        $result = file_get_contents($url);

        return $result === false ? null : $result;
    }

    /**
     * Substitute for mkdir.
     *
     * @throws FileException
     */
    public function makeDir(int $permissions = 0o777, int $flags = 0): self
    {
        $dir = new Dir($this->path);
        $dir->make($permissions, $flags);

        return $this;
    }

    /**
     * Substitute for link.
     *
     * @throws FileException
     */
    public function makeHardLink(string $link): self
    {
        if (! link($this->path, $link)) {
            throw new FileException(
                sprintf('Unable to create hard link "%s" to file "%s"', $link, $this->path),
            );
        }

        return $this;
    }

    /**
     * Substitute for symlink.
     *
     * The "filename" property should be the target.
     *
     * @throws FileException
     */
    public function makeSymlink(string $link): self
    {
        if (symlink($this->path, $link) === false) {
            throw new FileException(
                sprintf('Unable to link "%s" to file "%s"', $link, $this->path),
            );
        }

        return $this;
    }

    /**
     * Substitute for move_uploaded_file.
     *
     * @throws FileException
     */
    public function moveUpload(string $target): self
    {
        if (! move_uploaded_file($this->path, $target)) {
            throw new FileException(
                sprintf('Unable to move uploaded file "%s" to "%s"', $this->path, $target),
            );
        }

        $this->path = $target;

        return $this;
    }

    /**
     * Substitute for file_exists that throws exception if not existing.
     */
    public function mustExist(): self
    {
        if (! file_exists($this->path)) {
            throw new FileException(sprintf('File does not exist: "%s"', $this->path));
        }

        return $this;
    }

    /**
     * Substitute for rename.
     *
     * @throws FileException
     */
    public function rename(string $target): self
    {
        if (rename($this->path, $target, $this->context) === false) {
            throw new FileException(
                sprintf('Unable to rename file from "%s" to "%s"', $this->path, $target),
            );
        }

        // Update filename to new name.
        $this->path = $target;

        return $this;
    }

    /**
     * Substitute for realpath.
     *
     * Also updates the filename property.
     *
     * @throws FileException
     */
    public function resolve(): string
    {
        $result = realpath($this->path);
        if ($result === false) {
            throw new FileException(sprintf('Unable to get real path on "%s"', $this->path));
        }

        $this->path = $result;

        return $result;
    }

    /**
     * Substitute for file_put_contents.
     *
     * @throws FileException
     */
    public function saveString(mixed $data, int $flags = 0): int
    {
        $phpFlags = 0;
        if (($flags & self::APPEND) !== 0) {
            $phpFlags |= FILE_APPEND;
        }

        if (($flags & self::LOCK) !== 0) {
            $phpFlags |= LOCK_EX;
        }

        if (($flags & self::USE_INCLUDE_PATH) !== 0) {
            $phpFlags |= FILE_USE_INCLUDE_PATH;
        }

        $result = file_put_contents($this->path, $data, $phpFlags, $this->context);
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
        $result = filesize($this->path);
        if ($result === false) {
            throw new FileException(sprintf('Unable to get size of file "%s"', $this->path));
        }

        return $result;
    }

    /**
     * Substitute for touch.
     *
     * @throws FileException
     */
    public function touch(?int $mtime = null, ?int $atime = null): self
    {
        $result = touch($this->path, $mtime, $atime);
        if ($result === false) {
            throw new FileException(
                sprintf(
                    'Unable set file access and modification times on file "%s"',
                    $this->path,
                ),
            );
        }

        return $this;
    }
}
