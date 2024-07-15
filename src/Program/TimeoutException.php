<?php

declare(strict_types=1);

namespace DouglasGreen\Utility\Program;

/**
 * Thrown for operations that exceed a time limit.
 * Example: Script execution timeout, network request timeout.
 */
class TimeoutException extends ProgramException {}
