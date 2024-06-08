<?php

declare(strict_types=1);

namespace DouglasGreen\Utility;

use DouglasGreen\Utility\Exceptions\FileSystem\DirectoryException;

/**
 * Directory utility class to throw exceptions when basic operations fail.
 */
class Dir
{
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
    public function make(
        int $permissions = 0o777,
        bool $recursive = false,
    ): void {
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
}
