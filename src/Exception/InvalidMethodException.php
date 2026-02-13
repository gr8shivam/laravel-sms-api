<?php

declare(strict_types=1);

namespace Gr8Shivam\SmsApi\Exception;

/**
 * Invalid HTTP Method Exception
 *
 * Thrown when an unsupported HTTP method is specified in gateway configuration.
 */
class InvalidMethodException extends Exception
{
    /**
     * Create exception for unsupported HTTP method
     *
     * @param string $method The unsupported HTTP method
     * @return self
     */
    public static function unsupportedMethod(string $method): self
    {
        return new static(
            "HTTP method '{$method}' is not supported. Only GET and POST are allowed."
        );
    }
}
