<?php

declare(strict_types=1);

namespace DouglasGreen\Utility\FileSystem;

use DouglasGreen\Utility\Data\FlagChecker;
use DouglasGreen\Utility\Data\FlagHandler;
use DouglasGreen\Utility\Regex\Regex;
use Stringable;

/**
 * The functions in this class depend on a file path string, not an open file.
 * See File for other file functions.
 */
class Path implements FlagHandler, Stringable
{
    public const APPEND = 1;

    public const IGNORE_NEW_LINES = 2;

    public const LOCK = 4;

    public const SKIP_EMPTY_LINES = 8;

    public const USE_BINARY = 16;

    public const USE_INCLUDE_PATH = 32;

    protected string $path;

    public static function getFlagChecker(int $flags): FlagChecker
    {
        $flagNames = [
            'append' => self::APPEND,
            'ignoreNewLines' => self::IGNORE_NEW_LINES,
            'lock' => self::LOCK,
            'skipEmptyLines' => self::SKIP_EMPTY_LINES,
            'useBinary' => self::USE_BINARY,
            'useIncludePath' => self::USE_INCLUDE_PATH,
        ];
        return new FlagChecker($flagNames, $flags);
    }

    /**
     * @param ?resource $context
     */
    public function __construct(
        ?string $path = null,
        protected $context = null
    ) {
        if ($path === null) {
            $path = DirUtil::getCurrent();
        }

        $this->path = $path;
    }

    public function __toString(): string
    {
        return $this->path;
    }

    /**
     * Add a subpath.
     */
    public function addSubpath(string $subpath): self
    {
        // Ensure the current filename ends with a directory separator
        if (substr($this->path, -1) !== DIRECTORY_SEPARATOR) {
            $this->path .= DIRECTORY_SEPARATOR;
        }

        // Ensure the subpath does not start with a directory separator
        if (substr($subpath, 0, 1) === DIRECTORY_SEPARATOR) {
            $subpath = ltrim($subpath, DIRECTORY_SEPARATOR);
        }

        $this->path .= $subpath;

        return $this;
    }

    /**
     * Substitute for md5_file.
     *
     * @throws FileException
     */
    public function calcMd5(int $flags = 0): string
    {
        $flagChecker = static::getFlagChecker($flags);
        $useBinary = $flagChecker->get('useBinary');
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
            throw new FileException(sprintf('Unable to change group of file "%s"', $this->path));
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
            throw new FileException(sprintf('Unable to change owner of file "%s"', $this->path));
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

        // Preserve the timestamp of the source file
        $timestamp = filemtime($this->path);
        if ($timestamp === false) {
            throw new FileException(
                sprintf('Unable to get the file modification time for "%s"', $this->path),
            );
        }

        if (! touch($target, $timestamp)) {
            throw new FileException(
                sprintf('Unable to set the file modification time for "%s"', $target),
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
     * Get the type of a file based on its extension or other info.
     *
     * @todo Use the return type of "file" command if available.
     * Example: file -b bin/task.php
     * a /usr/bin/env php script, ASCII text executable
     */
    public function getFileType(): ?string
    {
        if (! str_contains($this->path, '.')) {
            // @todo Use file here instead of this.
            $file = new File($this->path);
            $line = $file->getLine();
            if ($line === null) {
                return null;
            }

            $match = Regex::match('/^#!.*\b(\w+)$/', $line);
            if ($match !== []) {
                return $this->getExtensionType($match[1]);
            }
        } else {
            $match = Regex::match('/\.(\w+)$/', $this->path);
            if ($match !== []) {
                return $this->getExtensionType($match[1]);
            }
        }

        return null;
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
            ARRAY_FILTER_USE_BOTH,
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
            throw new FileException(sprintf('Unable to get permissions of file "%s"', $this->path));
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
            ARRAY_FILTER_USE_BOTH,
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
    public function isDirectory(): bool
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
     * Substitute for is_link.
     */
    public function isLink(): bool
    {
        return is_link($this->path);
    }

    /**
     * Substitute for is_readable.
     */
    public function isReadable(): bool
    {
        return is_readable($this->path);
    }

    /**
     * Check if two files are the same by comparing their realpath.
     */
    public function isSame(self|string $other): bool
    {
        if (is_string($other)) {
            $other = new self($other);
        }

        return $this->resolve() === $other->resolve();
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
        $flagChecker = static::getFlagChecker($flags);
        $useIncludePath = $flagChecker->get('useIncludePath');
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
     * @throws FileException
     */
    public function loadLines(int $flags = 0): array
    {
        $flagChecker = static::getFlagChecker($flags);
        $phpFlags = 0;
        if ($flagChecker->get('ignoreNewLines')) {
            $phpFlags |= FILE_IGNORE_NEW_LINES;
        }

        if ($flagChecker->get('skipEmptyLines')) {
            $phpFlags |= FILE_SKIP_EMPTY_LINES;
        }

        if ($flagChecker->get('useIncludePath')) {
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
        $flagChecker = static::getFlagChecker($flags);
        $useIncludePath = $flagChecker->get('useIncludePath');
        $result = file_get_contents($this->path, $useIncludePath, $this->context, $offset, $length);

        if ($result === false) {
            throw new FileException('Unable to load file to string');
        }

        return $result;
    }

    /**
     * Substitute for mkdir.
     */
    public function makeDirectory(int $permissions = 0o777, int $flags = 0): self
    {
        $directory = new Directory($this->path);
        $directory->make($permissions, $flags);

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
     *
     * @throws FileException
     */
    public function mustExist(): self
    {
        if (! file_exists($this->path)) {
            throw new FileException(sprintf('File does not exist: "%s"', $this->path));
        }

        return $this;
    }

    /**
     * Remove the base path and get the relative subpath from an absolute path.
     */
    public function removeBase(string $absolutePath): string
    {
        $base = $this->path;

        // Ensure the base path ends with a directory separator
        if (substr($base, -1) !== DIRECTORY_SEPARATOR) {
            $base .= DIRECTORY_SEPARATOR;
        }

        // Check if the absolute path starts with the base path
        if (str_starts_with($absolutePath, $base)) {
            // Remove the base path from the absolute path to get the relative subpath
            return substr($absolutePath, strlen($base));
        }

        // If the absolute path does not contain the base path, return it instead
        return $absolutePath;
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
        $flagChecker = static::getFlagChecker($flags);
        $phpFlags = 0;
        if ($flagChecker->get('append')) {
            $phpFlags |= FILE_APPEND;
        }

        if ($flagChecker->get('lock')) {
            $phpFlags |= LOCK_EX;
        }

        if ($flagChecker->get('useIncludePath')) {
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
                sprintf('Unable set file access and modification times on file "%s"', $this->path),
            );
        }

        return $this;
    }

    protected function getExtensionType(string $extension): ?string
    {
        return match ($extension) {
            'bash', 'sh' => 'bash',
            'css' => 'css',
            'csv', 'pdv', 'tsv', 'txt' => 'data',
            'jpg', 'jpeg', 'png', 'gif', 'bmp', 'tiff', 'svg', 'webp' => 'images',
            'js', 'ts' => 'js',
            'json' => 'json',
            'md' => 'md',
            'php' => 'php',
            'sql' => 'sql',
            'xml', 'xsd', 'xsl', 'xslt', 'xhtml' => 'xml',
            'yaml', 'yml' => 'yaml',
            default => null,
        };
    }
}
