<?php

declare(strict_types=1);

namespace DouglasGreen\Utility\Exceptions\Program;

/**
 * Thrown for attempt to violate program security.
 * Example: Unauthorized access attempt, CSRF token mismatch.
 */
class SecurityException extends ProgramException {}
