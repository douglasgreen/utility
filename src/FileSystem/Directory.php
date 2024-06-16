<?php

declare(strict_types=1);

namespace DouglasGreen\Utility\FileSystem;

use Directory as PhpDirectory;
use DouglasGreen\Utility\Data\FlagChecker;
use DouglasGreen\Utility\Data\FlagHandler;

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
    public const RECURSIVE = 1;

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

    public static function getFlagChecker(int $flags): FlagChecker
    {
        $flagNames = [
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
        protected string $path,
        protected $context = null
    ) {}

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
    public function remove(): void
    {
        if (rmdir($this->path, $this->context) === false) {
            throw new DirectoryException(sprintf('Unable to remove directory "%s"', $this->path));
        }
    }

    /**
     * Substitute for scandir.
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
