<?php

declare(strict_types=1);

namespace DouglasGreen\Utility\FileSystem;

use Directory as PhpDirectory;
use DouglasGreen\Utility\Data\FlagChecker;
use DouglasGreen\Utility\Data\FlagHandler;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Directory utility class to throw exceptions when basic operations fail.
 *
 * Manages functions on a directory name.
 */
class Directory implements FlagHandler
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

    protected readonly string $path;

    public static function getFlagChecker(int $flags): FlagChecker
    {
        $flagNames = [
            'noDotDirs' => self::NO_DOT_DIRS,
            'recursive' => self::RECURSIVE,
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

        $this->path = $path;
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
                } else {
                    $files[] = $path;
                }
            }
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
    public function make(int $permissions = 0o777, int $flags = 0): self
    {
        if (is_dir($this->path)) {
            $path = new Path($this->path);
            $path->changeMode($permissions);
            return $this;
        }

        $flagChecker = static::getFlagChecker($flags);
        $recursive = $flagChecker->get('recursive');
        if (mkdir($this->path, $permissions, $recursive, $this->context) === false) {
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
        $result = tempnam($this->path, $prefix);
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
    public function remove(int $flags = 0): void
    {
        // OK if path doesn't exist.
        if (! is_dir($this->path)) {
            return;
        }

        $flagChecker = static::getFlagChecker($flags);
        $recursive = $flagChecker->get('recursive');

        if ($recursive) {
            $this->removeRecursive();
            return;
        }

        if (rmdir($this->path, $this->context) === false) {
            throw new DirectoryException(sprintf('Unable to remove directory "%s"', $this->path));
        }
    }

    /**
     * Recursively remove the contents of a directory but leave the directory.
     */
    public function removeContents(): void
    {
        // OK if path doesn't exist.
        if (! is_dir($this->path)) {
            return;
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
    }

    /**
     * Recursively remove the contents of a directory including the directory.
     *
     * Can also be called with $this->remove(Directory::RECURSIVE).
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
            return array_diff($result, ['..', '.']);
        }

        return $result;
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
