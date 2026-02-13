<?php

/**
 * SMS API Gateway Examples
 * 
 * ⚠️  IMPORTANT: This file contains EXAMPLES ONLY - Not for direct use!
 * 
 * These examples show how to configure popular SMS providers. To use:
 * 
 * 1. Find your SMS provider below
 * 2. Copy the configuration to config/sms-api.php
 * 3. Add the required environment variables to your .env file
 * 4. Rename the key (e.g., 'twilio_example' → 'twilio')
 * 
 * ⚠️  Config Caching Note:
 * The env() calls in these examples are FINE when copied to your main
 * config/sms-api.php file. Laravel caches config values, not env() calls.
 * Just don't load this examples file directly in production.
 * 
 * NAMING CONVENTION:
 * All examples end with '_example' to prevent conflicts with your actual
 * configurations.
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Twilio (Global) - Most Popular Worldwide
    |--------------------------------------------------------------------------
    | Website: https://www.twilio.com
    | Regions: Global
    | Auth Methods: Account SID + Auth Token OR API Key + Secret
    |
    | .env Variables:
    | TWILIO_ACCOUNT_SID=ACxxxxxxxxxxxxxxxxxxxxx
    | TWILIO_AUTH_TOKEN=your_auth_token
    | TWILIO_FROM_NUMBER=+15551234567
    */
    
    // Method 1: Using Account SID + Auth Token (Legacy, still widely used)
    'twilio_legacy_example' => [
        'method' => 'POST',
        'url' => 'https://api.twilio.com/2010-04-01/Accounts/' . env('TWILIO_ACCOUNT_SID') . '/Messages.json',
        'params' => [
            'send_to_param_name' => 'To',
            'msg_param_name' => 'Body',
            'others' => [
                'From' => env('TWILIO_FROM_NUMBER'),
            ],
        ],
        'headers' => [
            'Authorization' => 'Basic ' . base64_encode(env('TWILIO_ACCOUNT_SID') . ':' . env('TWILIO_AUTH_TOKEN')),
        ],
        'add_code' => true,
    ],

    // Method 2: Using API Key + Secret (Recommended by Twilio for better security)
    'twilio_apikey_example' => [
        'method' => 'POST',
        'url' => 'https://api.twilio.com/2010-04-01/Accounts/' . env('TWILIO_ACCOUNT_SID') . '/Messages.json',
        'params' => [
            'send_to_param_name' => 'To',
            'msg_param_name' => 'Body',
            'others' => [
                'From' => env('TWILIO_FROM_NUMBER'),
            ],
        ],
        'headers' => [
            'Authorization' => 'Basic ' . base64_encode(env('TWILIO_API_KEY') . ':' . env('TWILIO_API_SECRET')),
        ],
        'add_code' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | MSG91 (India & South Asia)
    |--------------------------------------------------------------------------
    | Website: https://msg91.com
    | Regions: India, Bangladesh, Pakistan, Sri Lanka
    | Auth: Auth Key
    |
    | .env Variables:
    | MSG91_AUTH_KEY=your_auth_key
    | MSG91_SENDER_ID=SENDERID
    */
    'msg91_example' => [
        'method' => 'POST',
        'url' => 'https://control.msg91.com/api/v5/flow/',
        'params' => [
            'send_to_param_name' => 'mobiles',
            'msg_param_name' => 'message',
            'others' => [
                'authkey' => env('MSG91_AUTH_KEY'),
                'sender' => env('MSG91_SENDER_ID'),
                'route' => '4', // 1=Promotional, 4=Transactional
            ],
        ],
        'json' => true,
        'add_code' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Vonage (formerly Nexmo) - Global
    |--------------------------------------------------------------------------
    | Website: https://www.vonage.com / https://dashboard.nexmo.com
    | Regions: Global (200+ countries)
    | Auth: API Key + Secret
    |
    | .env Variables:
    | VONAGE_API_KEY=your_api_key
    | VONAGE_API_SECRET=your_api_secret
    | VONAGE_FROM=YourBrand
    */
    'vonage_example' => [
        'method' => 'POST',
        'url' => 'https://rest.nexmo.com/sms/json',
        'params' => [
            'send_to_param_name' => 'to',
            'msg_param_name' => 'text',
            'others' => [
                'api_key' => env('VONAGE_API_KEY'),
                'api_secret' => env('VONAGE_API_SECRET'),
                'from' => env('VONAGE_FROM'),
            ],
        ],
        'json' => true,
        'add_code' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Plivo (Global)
    |--------------------------------------------------------------------------
    | Website: https://www.plivo.com
    | Regions: Global
    | Auth: Basic Authentication (Auth ID + Auth Token)
    |
    | .env Variables:
    | PLIVO_AUTH_ID=MAxxxxxxxxxxxxxxxxxxxxx
    | PLIVO_AUTH_TOKEN=your_auth_token
    | PLIVO_FROM_NUMBER=+15551234567
    */
    'plivo_example' => [
        'method' => 'POST',
        'url' => 'https://api.plivo.com/v1/Account/' . env('PLIVO_AUTH_ID') . '/Message/',
        'params' => [
            'send_to_param_name' => 'dst',
            'msg_param_name' => 'text',
            'others' => [
                'src' => env('PLIVO_FROM_NUMBER'),
            ],
        ],
        'headers' => [
            'Authorization' => 'Basic ' . base64_encode(env('PLIVO_AUTH_ID') . ':' . env('PLIVO_AUTH_TOKEN')),
        ],
        'json' => true,
        'add_code' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Sinch (Global)
    |--------------------------------------------------------------------------
    | Website: https://www.sinch.com
    | Regions: Global
    | Auth: Bearer Token
    |
    | .env Variables:
    | SINCH_SERVICE_PLAN_ID=your_service_plan_id
    | SINCH_API_TOKEN=your_api_token
    | SINCH_FROM_NUMBER=+15551234567
    */
    'sinch_example' => [
        'method' => 'POST',
        'url' => 'https://sms.api.sinch.com/xms/v1/' . env('SINCH_SERVICE_PLAN_ID') . '/batches',
        'params' => [
            'send_to_param_name' => 'to',
            'msg_param_name' => 'body',
            'others' => [
                'from' => env('SINCH_FROM_NUMBER'),
            ],
        ],
        'headers' => [
            'Authorization' => 'Bearer ' . env('SINCH_API_TOKEN'),
        ],
        'json' => true,
        'jsonToArray' => true,
        'add_code' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Textlocal (India & UK)
    |--------------------------------------------------------------------------
    | Website: https://www.textlocal.in (India) / https://www.textlocal.com (UK)
    | Regions: India, United Kingdom
    | Auth: API Key
    |
    | .env Variables:
    | TEXTLOCAL_API_KEY=your_api_key
    | TEXTLOCAL_SENDER_ID=TXTLCL
    */
    'textlocal_example' => [
        'method' => 'POST',
        'url' => 'https://api.textlocal.in/send/', // Use .com for UK
        'params' => [
            'send_to_param_name' => 'numbers',
            'msg_param_name' => 'message',
            'others' => [
                'apikey' => env('TEXTLOCAL_API_KEY'),
                'sender' => env('TEXTLOCAL_SENDER_ID'),
            ],
        ],
        'add_code' => false, // Expects numbers in 91XXXXXXXXXX format
    ],

    /*
    |--------------------------------------------------------------------------
    | Gupshup (India & Global)
    |--------------------------------------------------------------------------
    | Website: https://www.gupshup.io
    | Regions: India, Middle East, Southeast Asia
    | Auth: User ID + Password
    |
    | .env Variables:
    | GUPSHUP_USER_ID=your_user_id
    | GUPSHUP_PASSWORD=your_password
    */
    'gupshup_example' => [
        'method' => 'POST',
        'url' => 'https://enterprise.smsgupshup.com/GatewayAPI/rest',
        'params' => [
            'send_to_param_name' => 'phone_number',
            'msg_param_name' => 'message',
            'others' => [
                'userid' => env('GUPSHUP_USER_ID'),
                'password' => env('GUPSHUP_PASSWORD'),
                'method' => 'sendMessage',
                'msg_type' => 'TEXT',
                'auth_scheme' => 'plain',
            ],
        ],
        'add_code' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | ClickSend (Global)
    |--------------------------------------------------------------------------
    | Website: https://www.clicksend.com
    | Regions: Global (100+ countries)
    | Auth: Basic Authentication (Username + API Key)
    |
    | .env Variables:
    | CLICKSEND_USERNAME=your_username
    | CLICKSEND_API_KEY=your_api_key
    | CLICKSEND_FROM=YourBrand
    */
    'clicksend_example' => [
        'method' => 'POST',
        'url' => 'https://rest.clicksend.com/v3/sms/send',
        'params' => [
            'send_to_param_name' => 'to',
            'msg_param_name' => 'body',
            'others' => [
                'from' => env('CLICKSEND_FROM'),
            ],
        ],
        'headers' => [
            'Authorization' => 'Basic ' . base64_encode(env('CLICKSEND_USERNAME') . ':' . env('CLICKSEND_API_KEY')),
        ],
        'json' => true,
        'wrapper' => 'messages',
        'add_code' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | MessageBird (Global)
    |--------------------------------------------------------------------------
    | Website: https://www.messagebird.com
    | Regions: Global (Europe, Americas, Asia)
    | Auth: Access Key (API Key in Header)
    |
    | .env Variables:
    | MESSAGEBIRD_API_KEY=your_api_key
    | MESSAGEBIRD_ORIGINATOR=YourBrand
    */
    'messagebird_example' => [
        'method' => 'POST',
        'url' => 'https://rest.messagebird.com/messages',
        'params' => [
            'send_to_param_name' => 'recipients',
            'msg_param_name' => 'body',
            'others' => [
                'originator' => env('MESSAGEBIRD_ORIGINATOR'),
            ],
        ],
        'headers' => [
            'Authorization' => 'AccessKey ' . env('MESSAGEBIRD_API_KEY'),
        ],
        'json' => true,
        'jsonToArray' => true,
        'add_code' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Infobip (Global)
    |--------------------------------------------------------------------------
    | Website: https://www.infobip.com
    | Regions: Global (largest coverage worldwide)
    | Auth: API Key (App token)
    |
    | .env Variables:
    | INFOBIP_BASE_URL=xxxxx (e.g., api, eu1, us1)
    | INFOBIP_API_KEY=your_api_key
    | INFOBIP_FROM=YourBrand
    */
    'infobip_example' => [
        'method' => 'POST',
        'url' => 'https://' . env('INFOBIP_BASE_URL', 'api') . '.infobip.com/sms/2/text/advanced',
        'params' => [
            'send_to_param_name' => 'to',
            'msg_param_name' => 'text',
            'others' => [
                'from' => env('INFOBIP_FROM'),
            ],
        ],
        'headers' => [
            'Authorization' => 'App ' . env('INFOBIP_API_KEY'),
        ],
        'json' => true,
        'wrapper' => 'messages',
        'add_code' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Telnyx (Global)
    |--------------------------------------------------------------------------
    | Website: https://telnyx.com
    | Regions: Global (competitive pricing)
    | Auth: Bearer Token (API Key)
    |
    | .env Variables:
    | TELNYX_API_KEY=your_api_key
    | TELNYX_FROM_NUMBER=+15551234567
    | TELNYX_MESSAGING_PROFILE_ID=your_profile_id
    */
    'telnyx_example' => [
        'method' => 'POST',
        'url' => 'https://api.telnyx.com/v2/messages',
        'params' => [
            'send_to_param_name' => 'to',
            'msg_param_name' => 'text',
            'others' => [
                'from' => env('TELNYX_FROM_NUMBER'),
                'messaging_profile_id' => env('TELNYX_MESSAGING_PROFILE_ID'),
            ],
        ],
        'headers' => [
            'Authorization' => 'Bearer ' . env('TELNYX_API_KEY'),
        ],
        'json' => true,
        'add_code' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Bandwidth (USA)
    |--------------------------------------------------------------------------
    | Website: https://www.bandwidth.com
    | Regions: USA (domestic only)
    | Auth: Basic Authentication
    |
    | .env Variables:
    | BANDWIDTH_ACCOUNT_ID=your_account_id
    | BANDWIDTH_USERNAME=your_username
    | BANDWIDTH_PASSWORD=your_password
    | BANDWIDTH_APPLICATION_ID=your_app_id
    | BANDWIDTH_FROM_NUMBER=+15551234567
    */
    'bandwidth_example' => [
        'method' => 'POST',
        'url' => 'https://messaging.bandwidth.com/api/v2/users/' . env('BANDWIDTH_ACCOUNT_ID') . '/messages',
        'params' => [
            'send_to_param_name' => 'to',
            'msg_param_name' => 'text',
            'others' => [
                'from' => env('BANDWIDTH_FROM_NUMBER'),
                'applicationId' => env('BANDWIDTH_APPLICATION_ID'),
            ],
        ],
        'headers' => [
            'Authorization' => 'Basic ' . base64_encode(env('BANDWIDTH_USERNAME') . ':' . env('BANDWIDTH_PASSWORD')),
        ],
        'json' => true,
        'jsonToArray' => true,
        'add_code' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | 2Factor (India) - OTP Specialist
    |--------------------------------------------------------------------------
    | Website: https://2factor.in
    | Regions: India (specializes in OTP/transactional SMS)
    | Auth: API Key in URL
    |
    | .env Variables:
    | TWOFACTOR_API_KEY=your_api_key
    |
    | Note: 2Factor uses template-based messaging, not direct text
    */
    'twofactor_example' => [
        'method' => 'GET',
        'url' => 'https://2factor.in/API/V1/' . env('TWOFACTOR_API_KEY') . '/SMS/',
        'params' => [
            'send_to_param_name' => 'To',
            'msg_param_name' => 'TemplateName',
            'others' => [
                // Additional template variables can be added here
            ],
        ],
        'add_code' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Kaleyra (India & Global)
    |--------------------------------------------------------------------------
    | Website: https://www.kaleyra.com
    | Regions: India, Middle East, Africa, Southeast Asia
    | Auth: API Key
    |
    | .env Variables:
    | KALEYRA_API_KEY=your_api_key
    | KALEYRA_SENDER_ID=SENDER
    */
    'kaleyra_example' => [
        'method' => 'GET',
        'url' => 'https://api.kaleyra.io/v1/' . env('KALEYRA_API_KEY') . '/messages',
        'params' => [
            'send_to_param_name' => 'to',
            'msg_param_name' => 'body',
            'others' => [
                'sender' => env('KALEYRA_SENDER_ID'),
                'type' => 'TXN', // TXN=Transactional, PRO=Promotional
            ],
        ],
        'add_code' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Exotel (India)
    |--------------------------------------------------------------------------
    | Website: https://exotel.com
    | Regions: India, Southeast Asia, Middle East
    | Auth: API Key + API Token (Basic Auth)
    |
    | .env Variables:
    | EXOTEL_API_KEY=your_api_key
    | EXOTEL_API_TOKEN=your_api_token
    | EXOTEL_ACCOUNT_SID=your_account_sid
    | EXOTEL_SENDER_ID=EXOTEL
    */
    'exotel_example' => [
        'method' => 'POST',
        'url' => 'https://api.exotel.com/v1/Accounts/' . env('EXOTEL_ACCOUNT_SID') . '/Sms/send.json',
        'params' => [
            'send_to_param_name' => 'To',
            'msg_param_name' => 'Body',
            'others' => [
                'From' => env('EXOTEL_SENDER_ID'),
            ],
        ],
        'headers' => [
            'Authorization' => 'Basic ' . base64_encode(env('EXOTEL_API_KEY') . ':' . env('EXOTEL_API_TOKEN')),
        ],
        'add_code' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | SNS by Amazon Web Services (Global)
    |--------------------------------------------------------------------------
    | Website: https://aws.amazon.com/sns/
    | Regions: Global (all AWS regions)
    | Auth: AWS Signature Version 4 (complex)
    |
    | RECOMMENDATION: Use AWS SDK for PHP instead of REST API
    | 
    | AWS Signature V4 requires:
    | - HMAC-SHA256 signing of each request
    | - Canonical request formatting
    | - Timestamp and credential scope calculation
    | 
    | This is too complex for simple REST configuration.
    | Use official AWS SDK: composer require aws/aws-sdk-php
    |
    | Example with AWS SDK:
    | $sns = new Aws\Sns\SnsClient([
    |     'region' => 'us-east-1',
    |     'version' => 'latest',
    |     'credentials' => [
    |         'key' => env('AWS_ACCESS_KEY_ID'),
    |         'secret' => env('AWS_SECRET_ACCESS_KEY'),
    |     ]
    | ]);
    | $sns->publish([
    |     'PhoneNumber' => '+15551234567',
    |     'Message' => 'Your message'
    | ]);
    */
    
    // Note: AWS SNS example intentionally omitted due to complex auth requirements
    // Users should use the official AWS SDK instead

    /*
    |--------------------------------------------------------------------------
    | Generic Bearer Token Example
    |--------------------------------------------------------------------------
    | For any SMS provider that uses Bearer token authentication
    |
    | .env Variables:
    | SMS_BEARER_TOKEN=your_bearer_token_here
    | SMS_FROM_NUMBER=YourBrand
    */
    'generic_bearer_example' => [
        'method' => 'POST',
        'url' => 'https://api.yoursmsprovider.com/v1/send',
        'params' => [
            'send_to_param_name' => 'to',
            'msg_param_name' => 'message',
            'others' => [
                'from' => env('SMS_FROM_NUMBER', 'YourApp'),
            ],
        ],
        'headers' => [
            'Authorization' => 'Bearer ' . env('SMS_BEARER_TOKEN'),
            'Accept' => 'application/json',
        ],
        'json' => true,
        'add_code' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Generic API Key Example
    |--------------------------------------------------------------------------
    | For any SMS provider that uses API Key in header
    |
    | .env Variables:
    | SMS_API_KEY=your_api_key_here
    | SMS_FROM_NUMBER=YourBrand
    */
    'generic_apikey_example' => [
        'method' => 'POST',
        'url' => 'https://api.yoursmsprovider.com/v1/sms',
        'params' => [
            'send_to_param_name' => 'recipient',
            'msg_param_name' => 'text',
            'others' => [
                'sender' => env('SMS_FROM_NUMBER', 'YourApp'),
            ],
        ],
        'headers' => [
            'X-API-Key' => env('SMS_API_KEY'),
            'Accept' => 'application/json',
        ],
        'json' => true,
        'add_code' => true,
    ],

];