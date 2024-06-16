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
 *
 * @todo Make a static class to call this.
 */
class Directory implements FlagHandler
{
    public const RECURSIVE = 1;

    /**
     * Substitute for getcwd.
     *
     * @throws DirectoryException
     */
    public static function getCurrent(): string
    {
        $result = getcwd();
        if ($result === false) {
            throw new DirectoryException('Unable to get current working directory');
        }

        return $result;
    }

    public static function getFlagChecker(int $flags): FlagChecker
    {
        $flagNames = [
            'recursive' => self::RECURSIVE,
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
    public function scan(int $sortingOrder = SCANDIR_SORT_ASCENDING): array
    {
        $result = scandir($this->path, $sortingOrder, $this->context);
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
