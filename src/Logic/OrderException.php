<?php

declare(strict_types=1);

namespace DouglasGreen\Utility\Logic;

/**
 * Thrown when operations such as function calls are done in the wrong order.
 * Example: Attempting to use a resource before it has been initialized, or
 * calling a function that depends on another function that hasn't been executed
 * yet.
 */
class OrderException extends ProcessException {}
