<?php

declare(strict_types=1);

namespace DouglasGreen\Utility\FileSystem;

use DouglasGreen\Utility\Exceptions\FileSystem\DirectoryException;

/**
 * Directory utility class to throw exceptions when basic operations fail.
 *
 * Manages functions on a directory name.
 */
class Dir
{
    public const int RECURSIVE = 1;

    /**
     * Substitute for getcwd.
     *
     * @throws DirectoryException
     */
    public static function getCurrent(): string
    {
        $result = getcwd();
        if ($result === false) {
            throw new DirectoryException(
                'Unable to get current working directory',
            );
        }

        return $result;
    }

    /**
     * @param ?resource $context
     */
    public function __construct(
        protected string $directory,
        protected $context = null
    ) {}

    /**
     * Substitute for mkdir.
     *
     * @throws DirectoryException
     */
    public function make(int $permissions = 0o777, int $flags = 0): self
    {
        $recursive = (bool) ($flags & self::RECURSIVE);
        if (mkdir(
            $this->directory,
            $permissions,
            $recursive,
            $this->context
        ) === false) {
            throw new DirectoryException(sprintf(
                'Unable to make directory: "%s"',
                $this->directory
            ));
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
        $result = tempnam($this->directory, $prefix);
        if ($result === false) {
            throw new DirectoryException(
                sprintf(
                    'Unable to create temp file in directory "%s"',
                    $this->directory,
                ),
            );
        }

        return $result;
    }

    /**
     * Substitute for dir.
     *
     * @throws DirectoryException
     */
    public function open(): \Directory
    {
        $result = dir($this->directory, $this->context);
        if ($result === false) {
            throw new DirectoryException(sprintf(
                'Unable to open directory "%s"',
                $this->directory
            ));
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
        if (rmdir($this->directory, $this->context) === false) {
            throw new DirectoryException(sprintf(
                'Unable to remove directory "%s"',
                $this->directory
            ));
        }
    }

    /**
     * Substitute for scandir.
     *
     * @return list<string>
     * @throws DirectoryException
     */
    public function scan(
        int $sortingOrder = SCANDIR_SORT_ASCENDING
    ): array {
        $result = scandir($this->directory, $sortingOrder, $this->context);
        if ($result === false) {
            throw new DirectoryException(sprintf(
                'Unable to scan directory: "%s"',
                $this->directory
            ));
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
        $result = chdir($this->directory);
        if ($result === false) {
            throw new DirectoryException(sprintf(
                'Unable to change directory to: "%s"',
                $this->directory
            ));
        }

        return $this;
    }
}
