<?php

declare(strict_types=1);

namespace DouglasGreen\Utility\Exceptions\Data;

/**
 * Thrown when input has the wrong data type
 * Example: A function expects an integer, but a string is provided.
 */
class TypeException extends DataException {}
