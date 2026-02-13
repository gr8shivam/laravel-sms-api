<?php

declare(strict_types=1);

namespace Gr8Shivam\SmsApi\Notifications;

/**
 * SMS API Message Builder
 *
 * Fluent builder for constructing SMS notification messages with parameters,
 * headers, and metadata. Provides utility methods for validation and cost estimation.
 */
class SmsApiMessage
{
    /** @var string Message content */
    public string $content = '';
    
    /** @var array Additional API parameters */
    public array $params = [];
    
    /** @var array Additional HTTP headers */
    public array $headers = [];
    
    /** @var string Message type: 'text' or 'unicode' */
    public string $type = 'text';
    
    /** @var string|null Specific gateway to use */
    public ?string $gateway = null;

    /**
     * Create a new SMS message
     *
     * @param string $content Message content
     * @param array $params Additional parameters
     * @param array $headers Additional headers
     */
    public function __construct(string $content = '', array $params = [], array $headers = [])
    {
        $this->content = $content;
        $this->params = $params;
        $this->headers = $headers;
    }

    /**
     * Set the message content
     *
     * @param string $content Message text
     * @return $this
     */
    public function content(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    /**
     * Set specific gateway to use for this message
     *
     * @param string $gateway Gateway name from configuration
     * @return $this
     */
    public function gateway(string $gateway): self
    {
        $this->gateway = $gateway;
        return $this;
    }

    /**
     * Set all parameters at once
     *
     * @param array $params Parameters array
     * @return $this
     */
    public function params(array $params): self
    {
        $this->params = $params;
        return $this;
    }

    /**
     * Add a single parameter
     *
     * @param string $key Parameter name
     * @param mixed $value Parameter value
     * @return $this
     */
    public function addParam(string $key, mixed $value): self
    {
        $this->params[$key] = $value;
        return $this;
    }

    /**
     * Set all headers at once
     *
     * @param array $headers Headers array
     * @return $this
     */
    public function headers(array $headers): self
    {
        $this->headers = $headers;
        return $this;
    }

    /**
     * Add a single header
     *
     * @param string $key Header name
     * @param string $value Header value
     * @return $this
     */
    public function addHeader(string $key, string $value): self
    {
        $this->headers[$key] = $value;
        return $this;
    }

    /**
     * Mark message as unicode (for non-Latin characters)
     *
     * @return $this
     */
    public function unicode(): self
    {
        $this->type = 'unicode';
        return $this;
    }

    /**
     * Validate message content
     *
     * @return void
     * @throws \InvalidArgumentException When validation fails
     */
    public function validate(): void
    {
        if (empty($this->content)) {
            throw new \InvalidArgumentException('Message content cannot be empty');
        }

        $validationEnabled = config('sms-api.validation.enabled', false);
        
        if ($validationEnabled) {
            $maxLength = config('sms-api.validation.max_length', 1000);
            
            if (strlen($this->content) > $maxLength) {
                throw new \InvalidArgumentException(
                    "Message exceeds maximum length of {$maxLength} characters (current: " . strlen($this->content) . ")"
                );
            }
        }
    }

    /**
     * Static constructor for fluent interface
     *
     * @param string $content Message content
     * @return self
     */
    public static function create(string $content): self
    {
        return new self($content);
    }

    /**
     * Get message length in characters
     *
     * @return int
     */
    public function length(): int
    {
        return strlen($this->content);
    }

    /**
     * Check if message is empty
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->content);
    }

    /**
     * Get message preview (truncated)
     *
     * @param int $length Maximum length
     * @return string Truncated message with ellipsis if needed
     */
    public function preview(int $length = 50): string
    {
        if (strlen($this->content) <= $length) {
            return $this->content;
        }

        return substr($this->content, 0, $length) . '...';
    }

    /**
     * Estimate number of SMS segments required
     * 
     * Useful for cost estimation. Based on GSM 7-bit and Unicode standards.
     *
     * @return int Number of SMS segments (1, 2, 3, etc.)
     */
    public function estimateSegments(): int
    {
        $length = $this->length();

        if ($length === 0) {
            return 0;
        }

        // GSM 7-bit: 160 chars per segment, 153 for concatenated
        // Unicode/UTF-16: 70 chars per segment, 67 for concatenated
        $isUnicode = $this->type === 'unicode' || $this->containsUnicodeCharacters();

        if ($isUnicode) {
            return $length <= 70 ? 1 : (int)ceil($length / 67);
        }

        return $length <= 160 ? 1 : (int)ceil($length / 153);
    }

    /**
     * Detect if message contains unicode characters
     *
     * @return bool
     */
    private function containsUnicodeCharacters(): bool
    {
        return strlen($this->content) !== mb_strlen($this->content, 'UTF-8');
    }
}
