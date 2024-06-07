<?php

declare(strict_types=1);

namespace DouglasGreen\Utility\Exceptions\Program;

/**
 * Thrown when errors occur using proc_* functions
 * Example: Failure to execute a system process using proc_open().
 *
 * @see https://www.php.net/manual/en/ref.exec.php
 */
class SystemProcessException extends ProgramException {}
