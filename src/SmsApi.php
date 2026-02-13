<?php

declare(strict_types=1);

namespace Gr8Shivam\SmsApi;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Message;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Exception\RequestException;
use Gr8Shivam\SmsApi\Exception\Exception;
use Gr8Shivam\SmsApi\Exception\InvalidMethodException;

/**
 * SMS API Client
 *
 * A flexible SMS gateway client that works with any REST API SMS provider.
 * Supports multiple gateways, authentication methods, and request formats.
 */
class SmsApi
{
    private Client $client;
    private array $config = [];
    private ?string $gateway = null;
    private string $response = '';
    private int|string $responseCode = '';
    private ?string $countrycode = null;
    private array $wrapperParams = [];

    /**
     * Create a new SmsApi instance
     *
     * @param Client|null $client Optional Guzzle HTTP client instance
     */
    public function __construct(?Client $client = null)
    {
        $this->client = $client ?? new Client();
    }

    /**
     * Set the gateway to use for sending SMS
     *
     * @param string $gateway Gateway name from configuration
     * @return $this
     */
    public function gateway(string $gateway = ''): self
    {
        $this->gateway = $gateway;
        return $this;
    }

    /**
     * Set custom country code to prepend to phone numbers
     *
     * @param string $countrycode Country code without + symbol (e.g., '91' for India)
     * @return $this
     */
    public function countryCode(string $countrycode = ''): self
    {
        $this->countrycode = $countrycode;
        return $this;
    }

    /**
     * Add parameters that will be wrapped separately from regular params
     * 
     * Used when API requires nested payload structure
     *
     * @param array $wrapperParams Parameters to include in wrapped payload
     * @return $this
     */
    public function addWrapperParams(array $wrapperParams = []): self
    {
        $this->wrapperParams = $wrapperParams;
        return $this;
    }

    /**
     * Send SMS message to one or multiple recipients
     *
     * @param string|array $to Phone number(s) - string for single, array for bulk
     * @param string $message Message content to send
     * @param array|null $extra_params Additional API-specific parameters
     * @param array $extra_headers Additional HTTP headers
     * @return $this
     * @throws Exception When gateway configuration is invalid
     * @throws InvalidMethodException When HTTP method is unsupported
     * @throws \InvalidArgumentException When input validation fails
     */
    public function sendMessage(
        string|array $to,
        string $message,
        ?array $extra_params = null,
        array $extra_headers = []
    ): self {
        // Validate input parameters
        if (empty($to)) {
            throw new \InvalidArgumentException('Recipient phone number(s) cannot be empty');
        }

        if (empty(trim($message))) {
            throw new \InvalidArgumentException('Message content cannot be empty');
        }

        // Validate individual phone numbers
        $numbers = is_array($to) ? $to : [$to];
        foreach ($numbers as $number) {
            if (!is_string($number) || empty(trim($number))) {
                throw new \InvalidArgumentException('Invalid phone number: ' . var_export($number, true));
            }
        }

        // Load gateway configuration
        if (empty($this->gateway)) {
            $this->loadDefaultGateway();
        }

        $this->loadCredentialsFromConfig();

        // Extract configuration
        $requestmethod = $this->config['method'] ?? 'GET';
        $url = $this->config['url'];
        $isJson = isset($this->config['json']) && $this->config['json'];

        // Format mobile numbers according to config
        $mobile = $this->prepareMobile($to, (bool)($this->config['add_code'] ?? false), $isJson);

        // Prepare request parameters and headers
        $params = $this->config['params']['others'] ?? [];
        $headers = $this->config['headers'] ?? [];
        $wrapper = $this->config['wrapper'] ?? null;
        $wrapperParams = array_merge($this->wrapperParams, $this->config['wrapperParams'] ?? []);

        $send_to_param_name = $this->config['params']['send_to_param_name'];
        $msg_param_name = $this->config['params']['msg_param_name'];

        $sendvars = [];

        // Build request payload
        if ($wrapper) {
            // Build wrapped payload structure
            $sendvars[$send_to_param_name] = $mobile;
            $sendvars[$msg_param_name] = $message;
            
            if (!empty($wrapperParams)) {
                $sendvars = array_merge($sendvars, $wrapperParams);
            }
        } else {
            // Add to regular params
            $params[$send_to_param_name] = $mobile;
            $params[$msg_param_name] = $message;
        }

        // Merge extra parameters and headers
        if ($extra_params) {
            $params = array_merge($params, $extra_params);
        }

        if (!empty($extra_headers)) {
            $headers = array_merge($headers, $extra_headers);
        }

        // Send HTTP request
        try {
            $request = new Request($requestmethod, $url);
            
            $options = [
                'headers' => $headers
            ];

            // Build request options based on HTTP method
            if ($requestmethod === 'GET') {
                $options['query'] = $params;
            } elseif ($requestmethod === 'POST') {
                if ($wrapper) {
                    $payload = array_merge([$wrapper => [$sendvars]], $params);
                } else {
                    $payload = $params;
                }

                if ($isJson) {
                    $options['json'] = $payload;
                } else {
                    $options['form_params'] = $payload;
                }
            } else {
                throw InvalidMethodException::unsupportedMethod($requestmethod);
            }

            // Execute request
            $response = $this->getClient()->send($request, $options);

            $this->response = $response->getBody()->getContents();
            $this->responseCode = $response->getStatusCode();

            Log::info('SMS Gateway Response Code: ' . $this->responseCode);
            Log::info('SMS Gateway Response Body: ' . $this->response);
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $this->response = Message::bodySummary($response);
                $this->responseCode = $response->getStatusCode();

                Log::error('SMS Gateway Response Code: ' . $this->responseCode);
                Log::error('SMS Gateway Response Body: ' . $this->response);
            } else {
                // Handle connection timeouts and network errors
                $this->responseCode = 500;
                $this->response = $e->getMessage();
                Log::error('SMS Gateway Connection Error: ' . $e->getMessage());
            }
        } finally {
            // Reset state for Laravel Octane compatibility
            $this->wrapperParams = [];
        }

        return $this;
    }

    /**
     * Format mobile numbers according to gateway requirements
     *
     * @param string|array $to Phone number(s)
     * @param bool $addCode Whether to add country code
     * @param bool $isJson Whether request is JSON
     * @return string|array Formatted phone number(s)
     */
    private function prepareMobile(string|array $to, bool $addCode, bool $isJson): string|array
    {
        $mobile = $addCode ? $this->addCountryCode($to) : $to;

        // Convert array to comma-separated string for non-JSON requests
        if (!$isJson && is_array($mobile)) {
            return $this->composeBulkMobile($mobile);
        }

        // Wrap single number in array for JSON requests if configured
        if ($isJson && !is_array($mobile)) {
            $jsonToArray = $this->config['jsonToArray'] ?? true;
            return $jsonToArray ? [$mobile] : $mobile;
        }

        return $mobile;
    }

    /**
     * Load default gateway from configuration
     *
     * @return $this
     * @throws \Exception When default gateway is not configured
     */
    private function loadDefaultGateway(): self
    {
        $defaultacc = config('sms-api.default');
        if (empty($defaultacc)) {
            throw new \Exception(
                'No SMS gateway specified. Either call gateway() method or set SMS_API_DEFAULT_GATEWAY in .env'
            );
        }
        
        $this->gateway = $defaultacc;
        return $this;
    }

    /**
     * Load and validate gateway credentials from configuration
     *
     * @return $this
     * @throws Exception When configuration is missing or invalid
     */
    protected function loadCredentialsFromConfig(): self
    {
        $gateway = $this->gateway;
        $configname = 'sms-api.' . $gateway;
        $this->config = config($configname);

        if (empty($this->config)) {
            throw Exception::configNotFound((string)$gateway);
        }

        // Validate URL
        if (empty($this->config['url'])) {
            throw new \Exception("URL is required for gateway: {$gateway}");
        }

        if (!filter_var($this->config['url'], FILTER_VALIDATE_URL)) {
            throw new \Exception("Invalid URL for gateway '{$gateway}': {$this->config['url']}");
        }

        // Validate required parameter mappings
        if (!isset($this->config['params']['send_to_param_name'])) {
            throw new \Exception("Gateway '{$gateway}' missing required config: params.send_to_param_name");
        }

        if (!isset($this->config['params']['msg_param_name'])) {
            throw new \Exception("Gateway '{$gateway}' missing required config: params.msg_param_name");
        }

        return $this;
    }

    /**
     * Prepend country code to phone number(s)
     *
     * @param string|array $mobile Phone number(s)
     * @return string|array Phone number(s) with country code
     */
    private function addCountryCode(string|array $mobile): string|array
    {
        if (!$this->countrycode) {
            $this->countrycode = (string)config('sms-api.country_code', '91');
        }

        if (is_array($mobile)) {
            array_walk($mobile, function (&$value) {
                $value = $this->countrycode . $value;
            });
            return $mobile;
        }

        return $this->countrycode . $mobile;
    }

    /**
     * Convert array of mobile numbers to comma-separated string
     *
     * @param array $mobile Array of phone numbers
     * @return string Comma-separated phone numbers
     */
    private function composeBulkMobile(array $mobile): string
    {
        return implode(',', $mobile);
    }

    /**
     * Get the underlying HTTP client instance
     *
     * @return Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }

    /**
     * Get the raw response body from last request
     *
     * @return string
     */
    public function response(): string
    {
        return $this->response;
    }

    /**
     * Get HTTP status code from last request
     *
     * @return int|string Status code or empty string if no request made
     */
    public function getResponseCode(): int|string
    {
        return $this->responseCode;
    }

    /**
     * Check if the last request was successful
     *
     * @return bool True if status code is 2xx, false otherwise
     */
    public function isSuccessful(): bool
    {
        if ($this->responseCode === '') {
            return false;
        }

        return is_int($this->responseCode) && 
               $this->responseCode >= 200 && 
               $this->responseCode < 300;
    }
}
