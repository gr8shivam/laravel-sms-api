<?php

use Gr8Shivam\SmsApi\SmsApi;

if (!function_exists('smsapi')) {
    /**
     * Get SmsApi instance or send SMS directly
     * 
     * When called without arguments, returns the SmsApi instance.
     * When called with arguments, sends SMS directly.
     * 
     * @param string|array|null $to Mobile number(s)
     * @param string|null $message Message content
     * @param array|null $extra_params Additional parameters
     * @param array $extra_headers Additional headers
     * @return mixed SmsApi instance or result
     */
    function smsapi(
        string|array|null $to = null, 
        ?string $message = null, 
        ?array $extra_params = null, 
        array $extra_headers = []
    ): mixed {
        $smsapi = app('smsapi');
        
        if ($to !== null && $message !== null) {
            return $smsapi->sendMessage($to, $message, $extra_params, $extra_headers);
        }
        
        return $smsapi;
    }
}

if (!function_exists('send_sms')) {
    /**
     * Send SMS with exception handling
     * 
     * Returns true on success, false on failure. Automatically logs errors.
     * Useful for conditional SMS sending without try-catch blocks.
     * 
     * @param string|array $to Mobile number(s)
     * @param string $message Message content
     * @return bool True if SMS sent successfully, false otherwise
     */
    function send_sms(string|array $to, string $message): bool
    {
        try {
            $result = smsapi($to, $message);
            return $result->isSuccessful();
        } catch (\Exception $e) {
            report($e);
            return false;
        }
    }
}
