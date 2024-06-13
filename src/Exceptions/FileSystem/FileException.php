<?php

declare(strict_types=1);

namespace DouglasGreen\Utility\Exceptions\FileSystem;

/**
 * Thrown for issues related to file operations, such as:
 * - File not found
 * - File is not readable or writable
 * - File upload errors
 *
 * @see https://www.php.net/manual/en/ref.filesystem.php
 */
class FileException extends DiskException {}
