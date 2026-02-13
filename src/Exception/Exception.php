<?php

declare(strict_types=1);

namespace Gr8Shivam\SmsApi\Exception;

/**
 * Base SMS API Exception
 *
 * Custom exception class for SMS API related errors.
 * Provides static factory methods for common error scenarios.
 */
class Exception extends \Exception
{
    /**
     * Create exception for missing gateway configuration
     *
     * @param string $gateway Gateway name that wasn't found
     * @return self
     */
    public static function configNotFound(string $gateway): self
    {
        return new static("SMS gateway configuration not found for: {$gateway}");
    }

    /**
     * Create exception for invalid gateway response
     *
     * @param string $message Error message details
     * @return self
     */
    public static function invalidResponse(string $message): self
    {
        return new static("Invalid SMS gateway response: {$message}");
    }
}
