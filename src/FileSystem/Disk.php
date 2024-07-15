<?php

declare(strict_types=1);

namespace DouglasGreen\Utility\FileSystem;

use Stringable;

/**
 * Disk utility class to throw exceptions when basic operations fail.
 *
 * Manages functions on a directory name that refers to a disk.
 */
class Disk implements Stringable
{
    public function __construct(
        protected readonly string $directory
    ) {}

    public function __toString(): string
    {
        return $this->directory;
    }

    /**
     * Substitute for disk_free_space.
     *
     * @throws DiskException
     */
    public function getFreeSpace(): float
    {
        $result = disk_free_space($this->directory);
        if ($result === false) {
            throw new DiskException(
                sprintf('Unable to get available space on filesystem: "%s"', $this->directory),
            );
        }

        return $result;
    }

    /**
     * Substitute for disk_total_space.
     *
     * @throws DiskException
     */
    public function getTotalSpace(): float
    {
        $result = disk_total_space($this->directory);
        if ($result === false) {
            throw new DiskException(
                sprintf('Unable to get total space on filesystem: "%s"', $this->directory),
            );
        }

        return $result;
    }
}
