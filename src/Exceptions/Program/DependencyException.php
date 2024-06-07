<?php

declare(strict_types=1);

namespace DouglasGreen\Utility\Exceptions\Program;

/**
 * Thrown for issues related to missing or incorrect dependencies.
 * Example: Missing required PHP extension, class dependency not met.
 */
class DependencyException extends ProgramException {}
