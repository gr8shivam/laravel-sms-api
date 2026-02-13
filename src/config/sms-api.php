<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Country Code
    |--------------------------------------------------------------------------
    |
    | This country code will be prepended to phone numbers when 'add_code'
    | is enabled for a gateway. Change this to match your primary region.
    |
    */

    'country_code' => env('SMS_API_COUNTRY_CODE', '91'),

    /*
    |--------------------------------------------------------------------------
    | Default Gateway
    |--------------------------------------------------------------------------
    |
    | Specify which gateway configuration to use by default. This should match
    | one of the keys in your gateway configurations below.
    |
    */

    'default' => env('SMS_API_DEFAULT_GATEWAY', 'default'),

    /*
    |--------------------------------------------------------------------------
    | HTTP Client Timeouts
    |--------------------------------------------------------------------------
    |
    | Configure the timeout settings for HTTP requests to SMS gateways.
    | Values are in seconds.
    |
    */

    'timeout' => env('SMS_API_TIMEOUT', 30),
    'connect_timeout' => env('SMS_API_CONNECT_TIMEOUT', 10),

    /*
    |--------------------------------------------------------------------------
    | Message Validation
    |--------------------------------------------------------------------------
    |
    | Optional message validation. When enabled, messages will be validated
    | for length and content before sending.
    |
    */

    'validation' => [
        'enabled' => env('SMS_API_VALIDATION_ENABLED', false),
        'max_length' => env('SMS_API_MAX_LENGTH', 1000),
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification Channel Settings
    |--------------------------------------------------------------------------
    |
    | Configure how the SMS notification channel behaves on failures.
    |
    */

    'notifications' => [
        // If true, SMS failures are logged but don't stop other notifications
        // If false, SMS failures throw exceptions and stop the notification chain
        'fail_silently' => env('SMS_API_NOTIFICATIONS_FAIL_SILENTLY', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Gateway Configurations
    |--------------------------------------------------------------------------
    |
    | Define your SMS gateway configurations here. Each gateway requires:
    | - method: HTTP method (GET, POST, PUT, PATCH, DELETE)
    | - url: The API endpoint
    | - params: Parameter mappings and additional data
    | - headers: (optional) HTTP headers for authentication
    | - json: (optional) Send as JSON instead of form data
    | - wrapper: (optional) Wrap the request in a named object
    | - add_code: (optional) Auto-prepend country code
    |
    | See documentation for detailed examples.
    |
    */

    'default' => [
        'method' => 'POST',
        'url' => env('SMS_API_URL', 'https://api.example.com/send'),
        'params' => [
            'send_to_param_name' => 'to',
            'msg_param_name' => 'message',
            'others' => [
                'api_key' => env('SMS_API_KEY'),
                'sender_id' => env('SMS_SENDER_ID', 'YourApp'),
            ],
        ],
        'headers' => [
            'Accept' => 'application/json',
        ],
        'add_code' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Additional Gateway Examples
    |--------------------------------------------------------------------------
    |
    | Need examples for popular providers like Twilio, MSG91, Vonage, etc?
    | Publish the examples file:
    |
    | php artisan vendor:publish --tag=sms-api-examples
    |
    | This will create config/sms-api-examples.php with 15+ real-world examples.
    |
    */

];
