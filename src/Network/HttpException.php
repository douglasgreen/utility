<?php

declare(strict_types=1);

namespace DouglasGreen\Utility\Network;

/**
 * Thrown for HTTP-related errors, such as 404 or 500 status codes.
 * Example: Resource not found, internal server error.
 */
class HttpException extends NetworkException {}
