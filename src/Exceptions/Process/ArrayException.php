<?php

declare(strict_types=1);

namespace DouglasGreen\Utility\Exceptions\Process;

/**
 * Exception for accessing an invalid key or index in an array or collection or
 * other invalid array operation.
 * Example: Attempting to access an array element with a non-existent key, or
 * trying to perform an invalid operation on an array.
 */
class ArrayException extends ProcessException {}
