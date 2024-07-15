<?php

declare(strict_types=1);

namespace DouglasGreen\Utility\FileSystem;

use Directory as PhpDirectory;
use DouglasGreen\Utility\Data\FlagChecker;
use DouglasGreen\Utility\Data\FlagHandler;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Stringable;

/**
 * Directory utility class to throw exceptions when basic operations fail.
 *
 * Manages functions on a directory name.
 */
class Directory implements FlagHandler, Stringable
{
    /**
     * @var int
     */
    public const NO_DOT_DIRS = 1;

    /**
     * @var int
     */
    public const SORT_ASCENDING = 2;

    /**
     * @var int
     */
    public const SORT_DESCENDING = 4;

    /**
     * @var int
     */
    public const SORT_NONE = 8;

    protected readonly string $path;

    public static function getFlagChecker(int $flags): FlagChecker
    {
        $flagNames = [
            'noDotDirs' => self::NO_DOT_DIRS,
            'sortAscending' => self::SORT_ASCENDING,
            'sortDescending' => self::SORT_DESCENDING,
            'sortNone' => self::SORT_NONE,
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

        $this->path = rtrim($path, DIRECTORY_SEPARATOR);
    }

    public function __toString(): string
    {
        return $this->path;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return list<string>
     */
    public function listFiles(int $flags = 0): array
    {
        $files = [];

        if (is_dir($this->path)) {
            $dirContents = $this->scan($flags | self::NO_DOT_DIRS);

            foreach ($dirContents as $dirContent) {
                $path = $this->path . DIRECTORY_SEPARATOR . $dirContent;

                if (is_dir($path)) {
                    $dir = new self($path, $this->context);
                    $files = array_merge($files, $dir->listFiles($flags));
                } elseif (is_file($path)) {
                    $files[] = $path;
                }
            }
        } elseif (is_file($this->path)) {
            $files[] = $this->path;
        }

        return $files;
    }

    /**
     * Substitute for mkdir.
     *
     * If directory already exists, updates its permissions instead.
     *
     * @throws DirectoryException
     */
    public function make(int $permissions = 0o777): self
    {
        if (is_dir($this->path)) {
            $path = new Path($this->path);
            $path->changeMode($permissions);
            return $this;
        }

        if (mkdir($this->path, $permissions, false, $this->context) === false) {
            throw new DirectoryException(sprintf('Unable to make directory: "%s"', $this->path));
        }

        return $this;
    }

    /**
     * Substitute for mkdir that is recursive.
     */
    public function makeRecursive(int $permissions = 0o777): self
    {
        if (is_dir($this->path)) {
            $path = new Path($this->path);
            $path->changeMode($permissions);
            return $this;
        }

        if (mkdir($this->path, $permissions, true, $this->context) === false) {
            throw new DirectoryException(sprintf('Unable to make directory: "%s"', $this->path));
        }

        return $this;
    }

    /**
     * Substitute for tempnam.
     *
     * @throws DirectoryException
     */
    public function makeTemp(string $prefix): string
    {
        // Check if the directory does not exist or is not writable. Without this, tempnam() will
        // just use the /tmp directory.
        if (! is_dir($this->path) || ! is_writable($this->path)) {
            throw new DirectoryException(
                sprintf('Temp file directory "%s" does not exist or is not writable', $this->path),
            );
        }

        $result = tempnam($this->path, $prefix);

        // This exception hardly ever happens so it can't be unit tested.
        if ($result === false) {
            throw new DirectoryException(
                sprintf('Unable to create temp file in directory "%s"', $this->path),
            );
        }

        return $result;
    }

    /**
     * Substitute for dir.
     *
     * @throws DirectoryException
     */
    public function open(): PhpDirectory
    {
        $result = dir($this->path, $this->context);
        if ($result === false) {
            throw new DirectoryException(sprintf('Unable to open directory "%s"', $this->path));
        }

        return $result;
    }

    /**
     * Substitute for rmdir.
     *
     * @throws DirectoryException
     */
    public function remove(): void
    {
        // OK if path doesn't exist.
        if (! is_dir($this->path)) {
            return;
        }

        if (rmdir($this->path, $this->context) === false) {
            throw new DirectoryException(sprintf('Unable to remove directory "%s"', $this->path));
        }
    }

    /**
     * Recursively remove the contents of a directory but leave the directory.
     *
     * @throws DirectoryException
     * @throws FileException
     */
    public function removeContents(): self
    {
        // OK if path doesn't exist.
        if (! is_dir($this->path)) {
            return $this;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->path, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST,
        );

        foreach ($iterator as $file) {
            if ($file->isDir()) {
                // Don't delete current directory.
                if (PathUtil::isSame($this->path, $file->getPathname())) {
                    continue;
                }

                if (rmdir($file->getPathname(), $this->context) === false) {
                    throw new DirectoryException(
                        sprintf('Unable to remove directory "%s"', $file->getFilename()),
                    );
                }
            } elseif (unlink($file->getPathname(), $this->context) === false) {
                throw new FileException(
                    sprintf('Unable to delete file "%s"', $file->getFilename()),
                );
            }
        }

        return $this;
    }

    /**
     * Recursively remove the contents of a directory including the directory.
     */
    public function removeRecursive(): void
    {
        $this->removeContents();
        $this->remove();
    }

    /**
     * Substitute for scandir.
     *
     * Adds a NO_DOT_DIRS flag to exclude the . and .. directories used in Linux.
     *
     * @return list<string>
     * @throws DirectoryException
     */
    public function scan(int $flags = 0): array
    {
        $flagChecker = static::getFlagChecker($flags);
        $phpFlags = 0;
        if ($flagChecker->get('sortAscending')) {
            $phpFlags |= SCANDIR_SORT_ASCENDING;
        } elseif ($flagChecker->get('sortDescending')) {
            $phpFlags |= SCANDIR_SORT_DESCENDING;
        } else {
            $phpFlags |= SCANDIR_SORT_NONE;
        }

        $result = scandir($this->path, $phpFlags, $this->context);
        if ($result === false) {
            throw new DirectoryException(sprintf('Unable to scan directory: "%s"', $this->path));
        }

        if ($flagChecker->get('noDotDirs')) {
            $result = array_diff($result, ['..', '.']);
        }

        return array_values($result);
    }

    /**
     * Substitute for chdir.
     *
     * @throws DirectoryException
     */
    public function setCurrent(): self
    {
        $result = chdir($this->path);
        if ($result === false) {
            throw new DirectoryException(
                sprintf('Unable to change directory to: "%s"', $this->path),
            );
        }

        return $this;
    }
}
