<?php

declare(strict_types=1);

namespace DouglasGreen\Utility\Exceptions\FileSystem;

use DouglasGreen\Utility\Exceptions\BaseException;

/**
 * Thrown for issues related to disk operations, such as checking total space.
 *
 * @see https://www.php.net/manual/en/ref.filesystem.php
 */
class DiskException extends BaseException {}
