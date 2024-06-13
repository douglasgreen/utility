<?php

declare(strict_types=1);

namespace DouglasGreen\Utility\FileSystem;

/**
 * Thrown for failure of directory-related actions, such as:
 * - Directory is not empty and can't be deleted
 * - Path is not a directory, but a directory was expected
 * - Unable to change the directory
 */
class DirectoryException extends DiskException {}
