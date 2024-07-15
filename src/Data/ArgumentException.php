<?php

declare(strict_types=1);

namespace DouglasGreen\Utility\Data;

/**
 * Exception for invalid arguments passed to a method.
 * Example: A method expects a non-null string argument, but receives null or an
 * integer.
 */
class ArgumentException extends DataException {}
