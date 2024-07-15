<?php

declare(strict_types=1);

namespace DouglasGreen\Utility\Logic;

/**
 * Thrown when unable to parse a string.
 * Example: Failure to parse a date string, invalid JSON or XML string parsing.
 */
class ParseException extends ProcessException {}
