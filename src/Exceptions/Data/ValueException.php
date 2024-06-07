<?php

declare(strict_types=1);

namespace DouglasGreen\Utility\Exceptions\Data;

/**
 * Thrown when numeric input is out of range or not an accepted value
 * - A value for age is provided as -5, which is not valid.
 * - A value is not found on an enumerated list of accepted values.
 * - A value like an ID was duplicated when it should be unique.
 */
class ValueException extends DataException {}
