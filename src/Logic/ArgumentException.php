<?php

declare(strict_types=1);

namespace DouglasGreen\Utility\Logic;

/**
 * Exception for invalid arguments passed to a method.
 * Example: A method expects a non-null string argument, but receives null or an
 * integer.
 */
class ArgumentException extends ProcessException {}
