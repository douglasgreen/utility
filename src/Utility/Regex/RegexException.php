<?php

declare(strict_types=1);

namespace DouglasGreen\Utility\Regex;

use DouglasGreen\Utility\BaseException;

/**
 * Thrown when a regex returns false when applied due to being malformed.
 * Example: Providing an invalid regular expression pattern that causes a regex
 * function to fail.
 *
 * @see https://www.php.net/manual/en/ref.pcre.php
 */
class RegexException extends BaseException {}
