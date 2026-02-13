[![Packagist](https://img.shields.io/packagist/v/gr8shivam/laravel-sms-api.svg)](https://packagist.org/packages/gr8shivam/laravel-sms-api)
[![Packagist](https://img.shields.io/packagist/dt/gr8shivam/laravel-sms-api.svg)](https://packagist.org/packages/gr8shivam/laravel-sms-api)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](https://github.com/gr8shivam/laravel-sms-api/blob/master/LICENSE)
[![Laravel](https://img.shields.io/badge/Laravel-10%2B-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.1%2B-blue.svg)](https://php.net)

# Laravel SMS API

A modern, flexible Laravel package for integrating any SMS gateway that provides a REST API. Perfect for Laravel 10+ applications with full support for notifications, multiple gateways, and modern authentication methods.

#### [⭐ Star this repo](https://github.com/gr8shivam/laravel-sms-api) to show support!

---

## 📋 Table of Contents

- [Features](#-features)
- [Requirements](#-requirements)
- [Installation](#-installation)
- [Quick Start](#-quick-start)
- [Configuration](#-configuration)
  - [Basic Gateway](#basic-gateway-configuration)
  - [Advanced Gateway](#advanced-gateway-configuration)
  - [Authentication Methods](#authentication-methods)
  - [Special Parameters](#special-parameters)
- [Usage](#-usage)
  - [Helper Function](#using-helper-function)
  - [Facade](#using-facade)
  - [Notifications](#using-notifications)
- [Advanced Features](#-advanced-features)
- [Real Provider Examples](#-real-provider-examples)
- [Testing](#-testing)
- [Changelog](#-changelog)
- [Support](#-support)

---

## ✨ Features

- ✅ **Universal Compatibility** - Works with any REST API SMS provider
- ✅ **Multiple Gateways** - Configure and switch between multiple SMS providers
- ✅ **Laravel Notifications** - Full integration with Laravel's notification system
- ✅ **Modern Auth** - Support for Bearer tokens, API keys, Basic Auth, and custom headers
- ✅ **Bulk SMS** - Send to multiple recipients in a single call
- ✅ **JSON & Form Data** - Support for both JSON payloads and form-encoded requests
- ✅ **Request Wrapping** - Handle complex API structures with wrapper support
- ✅ **Type Safe** - Built with PHP 8.1+ strict types
- ✅ **Dependency Injection** - Modern Laravel service container integration
- ✅ **Comprehensive Logging** - Built-in request/response logging
- ✅ **Easy Testing** - Mock-friendly architecture

---

## 📌 Requirements

- PHP 8.1 or higher
- Laravel 10.x, 11.x, or 12.x
- Guzzle HTTP Client 7.8+

---

## 📦 Installation

### 1. Install via Composer

```bash
composer require gr8shivam/laravel-sms-api
```

### 2. Publish Configuration

```bash
php artisan vendor:publish --provider="Gr8Shivam\SmsApi\SmsApiServiceProvider"
```

This creates `config/sms-api.php` in your application.

### 3. Configure Your Gateway

Edit `config/sms-api.php` and add your SMS provider credentials (see [Configuration](#-configuration) below).

---

## 🚀 Quick Start

### Send Your First SMS

```php
use Gr8Shivam\SmsApi\SmsApiFacade as SmsApi;

// Simple usage
SmsApi::sendMessage("9876543210", "Hello from Laravel!");

// Or using helper
smsapi("9876543210", "Hello from Laravel!");
```

### Send to Multiple Recipients

```php
SmsApi::sendMessage(["9876543210", "9876543211"], "Hello everyone!");
```

### Get Response

```php
$response = SmsApi::sendMessage("9876543210", "Hello!")
    ->response();

$statusCode = SmsApi::sendMessage("9876543210", "Hello!")
    ->getResponseCode();

$isSuccess = SmsApi::sendMessage("9876543210", "Hello!")
    ->isSuccessful(); // Returns true for 2xx status codes
```

---

## ⚙️ Configuration

Open `config/sms-api.php` after publishing.

### Global Settings

```php
return [
    // Default country code (added to numbers automatically)
    'country_code' => env('SMS_API_COUNTRY_CODE', '91'),

    // Default gateway to use
    'default' => env('SMS_API_DEFAULT_GATEWAY', 'your_gateway'),

    // HTTP client timeouts
    'timeout' => env('SMS_API_TIMEOUT', 30),
    'connect_timeout' => env('SMS_API_CONNECT_TIMEOUT', 10),

    // Optional message validation
    'validation' => [
        'enabled' => env('SMS_API_VALIDATION_ENABLED', false),
        'max_length' => env('SMS_API_MAX_LENGTH', 1000),
    ],
];
```

---

### Basic Gateway Configuration

For simple GET/POST requests:

```php
'your_gateway' => [
    'method' => 'POST',  // GET, POST, PUT, PATCH, DELETE
    'url' => 'https://api.smsgateway.com/send',
    'params' => [
        'send_to_param_name' => 'mobile',    // Your provider's "to" param name
        'msg_param_name' => 'message',       // Your provider's "message" param name
        'others' => [
            'api_key' => env('SMS_GATEWAY_KEY'),
            'sender_id' => env('SMS_SENDER_ID'),
        ],
    ],
    'headers' => [
        'Accept' => 'application/json',
    ],
    'add_code' => true,  // Automatically add country code
],
```

---

### Advanced Gateway Configuration

For JSON requests with complex structures:

```php
'advanced_gateway' => [
    'method' => 'POST',
    'url' => 'https://api.provider.com/v2/send',
    'params' => [
        'send_to_param_name' => 'recipient',
        'msg_param_name' => 'text',
        'others' => [
            'priority' => 'high',
            'ttl' => 3600,
        ],
    ],
    'headers' => [
        'Authorization' => 'Bearer ' . env('SMS_API_TOKEN'),
        'Content-Type' => 'application/json',
    ],
    'json' => true,              // Send as JSON payload
    'jsonToArray' => true,       // Send single number as array: ["9876543210"]
    'wrapper' => 'data',         // Wrap payload in {"data": {...}}
    'wrapperParams' => [
        'campaign_id' => 'welcome_sms',
    ],
    'add_code' => true,
],
```

**Result payload example:**
```json
{
  "data": [
    {
      "recipient": ["919876543210"],
      "text": "Your message",
      "campaign_id": "welcome_sms"
    }
  ],
  "priority": "high",
  "ttl": 3600
}
```

---

### Authentication Methods

#### 1. Bearer Token (Most Modern APIs)

```php
'gateway' => [
    'headers' => [
        'Authorization' => 'Bearer ' . env('SMS_API_TOKEN'),
    ],
],
```

**In `.env`:**
```env
SMS_API_TOKEN=your_bearer_token_here
```

#### 2. API Key in Header

```php
'gateway' => [
    'headers' => [
        'X-API-Key' => env('SMS_API_KEY'),
    ],
],
```

#### 3. Basic Authentication

```php
'gateway' => [
    'headers' => [
        'Authorization' => 'Basic ' . base64_encode(env('SMS_USERNAME') . ':' . env('SMS_PASSWORD')),
    ],
],
```

#### 4. API Key in Parameters

```php
'gateway' => [
    'params' => [
        'others' => [
            'api_key' => env('SMS_API_KEY'),
        ],
    ],
],
```

---

### Special Parameters

#### `json` (boolean)
Send parameters as JSON payload instead of query string or form data.
```php
'json' => true,
```

#### `jsonToArray` (boolean)
When `json` is `true`, controls whether a single mobile number is sent as:
- `true`: `["9876543210"]` (array)
- `false`: `"9876543210"` (string)
```php
'jsonToArray' => false,
```

#### `wrapper` (string)
Wraps the JSON request in a named object. Required by some providers.
```php
'wrapper' => 'sms',  // Creates: {"sms": [{...}]}
```

#### `wrapperParams` (array)
Adds parameters **inside** the wrapper (separate from regular params).
```php
'wrapperParams' => [
    'campaign' => 'summer_sale',
    'priority' => 1,
],
```

#### `add_code` (boolean)
Automatically prepend country code to phone numbers.
```php
'add_code' => true,  // 9876543210 becomes 919876543210
```

---

## 📱 Usage

### Using Helper Function

The `smsapi()` helper provides the most convenient way to send SMS.

#### Basic Usage

```php
// Quick send
smsapi("9876543210", "Welcome to our platform!");

// Or
smsapi()->sendMessage("9876543210", "Welcome!");
```

#### With Extra Parameters

```php
smsapi("9876543210", "Your OTP is 1234", [
    'template_id' => 'OTP_TEMPLATE',
    'priority' => 'high'
]);
```

#### With Custom Headers

```php
smsapi("9876543210", "Hello", [], [
    'X-Custom-Header' => 'value',
    'X-Request-ID' => uniqid()
]);
```

#### Using Different Gateway

```php
smsapi()->gateway('backup_gateway')
    ->sendMessage("9876543210", "Message via backup gateway");
```

#### Using Different Country Code

```php
smsapi()->countryCode('1')  // USA
    ->sendMessage("5551234567", "Hello from USA!");
```

#### Bulk SMS

```php
$recipients = ["9876543210", "9876543211", "9876543212"];
smsapi($recipients, "Bulk message to all!");
```

#### With Wrapper Parameters

```php
smsapi()->addWrapperParams([
        'campaign' => 'newsletter',
        'tracking_id' => '12345'
    ])
    ->sendMessage("9876543210", "Newsletter message");
```

#### Method Chaining

```php
smsapi()
    ->gateway('primary_gateway')
    ->countryCode('91')
    ->addWrapperParams(['campaign' => 'promo'])
    ->sendMessage("9876543210", "Promotional offer!", [
        'template_id' => 'PROMO_123'
    ]);
```

---

### Using Facade

The facade provides the same functionality with explicit imports.

```php
use Gr8Shivam\SmsApi\SmsApiFacade as SmsApi;

// Basic usage
SmsApi::sendMessage("9876543210", "Hello!");

// With gateway selection
SmsApi::gateway('gateway_name')
    ->sendMessage("9876543210", "Hello!");

// With country code
SmsApi::countryCode('44')  // UK
    ->sendMessage("7911123456", "Hello from UK!");

// Bulk SMS
SmsApi::sendMessage(
    ["9876543210", "9876543211"],
    "Bulk message"
);

// Get response
$response = SmsApi::sendMessage("9876543210", "Test")
    ->response();

// Get status code
$code = SmsApi::sendMessage("9876543210", "Test")
    ->getResponseCode();

// Check success
$success = SmsApi::sendMessage("9876543210", "Test")
    ->isSuccessful();
```

---

### Using Notifications

Laravel SMS API integrates seamlessly with Laravel's notification system.

#### Step 1: Add Route to Your Model

In your `User` model (or any Notifiable model):

```php
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * Route notifications for SMS API channel
     */
    public function routeNotificationForSmsApi()
    {
        return $this->phone;  // Return the phone number column
    }
}
```

#### Step 2: Create Notification

```bash
php artisan make:notification WelcomeNotification
```

```php
namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Gr8Shivam\SmsApi\Notifications\SmsApiChannel;
use Gr8Shivam\SmsApi\Notifications\SmsApiMessage;

class WelcomeNotification extends Notification
{
    public function via($notifiable)
    {
        return [SmsApiChannel::class];
    }

    public function toSmsApi($notifiable)
    {
        return (new SmsApiMessage)
            ->content("Welcome {$notifiable->name}!");
    }
}
```

#### Step 3: Send Notification

```php
$user = User::find(1);
$user->notify(new WelcomeNotification());
```

#### Advanced Notification Examples

**With Parameters:**
```php
public function toSmsApi($notifiable)
{
    return (new SmsApiMessage)
        ->content("Your OTP is: {$this->otp}")
        ->params([
            'template_id' => 'OTP_VERIFY',
            'priority' => 'high'
        ]);
}
```

**With Headers:**
```php
public function toSmsApi($notifiable)
{
    return (new SmsApiMessage)
        ->content($this->message)
        ->params(['campaign' => 'marketing'])
        ->headers(['X-Campaign-ID' => '12345']);
}
```

**Unicode Message:**
```php
public function toSmsApi($notifiable)
{
    return (new SmsApiMessage)
        ->content("नमस्ते! Welcome")
        ->unicode();
}
```

**Using Static Constructor:**
```php
public function toSmsApi($notifiable)
{
    return SmsApiMessage::create("Hello {$notifiable->name}!")
        ->addParam('template_id', 'WELCOME_001')
        ->addHeader('X-Priority', 'high');
}
```

**Return String (Shorthand):**
```php
public function toSmsApi($notifiable)
{
    return "Welcome to our platform!";  // Automatically converted to SmsApiMessage
}
```

---

## 🔥 Advanced Features

### 1. Safe Sending Helper

Use `send_sms()` for operations where you want to catch exceptions:

```php
if (send_sms("9876543210", "Hello!")) {
    echo "SMS sent successfully!";
} else {
    echo "SMS failed. Error logged.";
}
```

### 2. Response Handling

```php
$sms = smsapi()->sendMessage("9876543210", "Hello!");

// Get raw response
$response = $sms->response();

// Get status code
$code = $sms->getResponseCode();

// Check if successful
if ($sms->isSuccessful()) {
    // 2xx status code
}
```

### 3. Message Validation

```php
$message = new SmsApiMessage("Your message here");

// Get message length
$length = $message->length();

// Check if empty
if ($message->isEmpty()) {
    // Handle empty message
}

// Estimate SMS segments (for cost calculation)
$segments = $message->estimateSegments();  // Returns 1, 2, 3...

// Get preview
$preview = $message->preview(50);  // First 50 characters
```

Enable validation in config:
```php
'validation' => [
    'enabled' => true,
    'max_length' => 1000,
],
```

Then validate:
```php
try {
    $message->validate();
} catch (\InvalidArgumentException $e) {
    // Handle validation error
}
```

### 4. Dynamic Gateway Selection

```php
$gateway = $user->isPremium() ? 'premium_gateway' : 'basic_gateway';

smsapi()->gateway($gateway)
    ->sendMessage($user->phone, "Relevant message");
```

### 5. Environment-based Configuration

```php
// .env
SMS_API_DEFAULT_GATEWAY=twilio
SMS_API_COUNTRY_CODE=1
SMS_API_TIMEOUT=60
TWILIO_ACCOUNT_SID=your_sid
TWILIO_AUTH_TOKEN=your_token
TWILIO_FROM_NUMBER=+15551234567
```

### 6. Multiple Recipients Handling

```php
// Array of numbers
$recipients = User::where('notify_sms', true)
    ->pluck('phone')
    ->toArray();

smsapi($recipients, "Important announcement!");
```

### 7. Logging

All requests and responses are automatically logged using Laravel's logging system:

```php
// Check logs/laravel.log for:
// [INFO] SMS Gateway Response Code: 200
// [INFO] SMS Gateway Response Body: {...}
// [ERROR] SMS Gateway Response Code: 400
// [ERROR] SMS Gateway Response Body: {...}
```

---

## 🌐 Real Provider Examples

### Twilio

```php
'twilio' => [
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
```

**Usage:**
```php
smsapi()->gateway('twilio')->sendMessage("5551234567", "Hello from Twilio!");
```

---

### MSG91

```php
'msg91' => [
    'method' => 'POST',
    'url' => 'https://control.msg91.com/api/v2/sendsms',
    'params' => [
        'send_to_param_name' => 'to',
        'msg_param_name' => 'message',
        'others' => [
            'authkey' => env('MSG91_AUTH_KEY'),
            'sender' => env('MSG91_SENDER_ID'),
            'route' => '4',
            'country' => '91',
        ],
    ],
    'json' => true,
    'wrapper' => 'sms',
    'add_code' => false,
],
```

**Usage:**
```php
smsapi()->gateway('msg91')->sendMessage("9876543210", "Hello from MSG91!");
```

---

### AWS SNS (via REST API)

```php
'aws_sns' => [
    'method' => 'POST',
    'url' => 'https://sns.us-east-1.amazonaws.com/',
    'params' => [
        'send_to_param_name' => 'PhoneNumber',
        'msg_param_name' => 'Message',
        'others' => [
            'Action' => 'Publish',
        ],
    ],
    'headers' => [
        'Authorization' => 'AWS4-HMAC-SHA256 ...',  // Use AWS SDK for proper signing
    ],
    'add_code' => true,
],
```

---

### Nexmo/Vonage

```php
'nexmo' => [
    'method' => 'POST',
    'url' => 'https://rest.nexmo.com/sms/json',
    'params' => [
        'send_to_param_name' => 'to',
        'msg_param_name' => 'text',
        'others' => [
            'api_key' => env('NEXMO_API_KEY'),
            'api_secret' => env('NEXMO_API_SECRET'),
            'from' => env('NEXMO_FROM'),
        ],
    ],
    'json' => true,
    'add_code' => true,
],
```

---

### Generic Bearer Token API

```php
'generic_api' => [
    'method' => 'POST',
    'url' => 'https://api.smsprovider.com/v1/send',
    'params' => [
        'send_to_param_name' => 'to',
        'msg_param_name' => 'message',
        'others' => [
            'sender' => 'YourApp',
        ],
    ],
    'headers' => [
        'Authorization' => 'Bearer ' . env('SMS_BEARER_TOKEN'),
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
    ],
    'json' => true,
    'add_code' => true,
],
```

---

## 🧪 Testing

### Running Tests

```bash
composer test
```

### Writing Tests

```php
use Gr8Shivam\SmsApi\Tests\AbstractTestCase;
use Gr8Shivam\SmsApi\SmsApiFacade as SmsApi;

class MyFeatureTest extends AbstractTestCase
{
    /** @test */
    public function it_sends_sms()
    {
        $this->mockSmsGateway(200, 'Success');

        $response = SmsApi::sendMessage("9876543210", "Test");

        $this->assertEquals(200, $response->getResponseCode());
        $this->assertTrue($response->isSuccessful());
    }
}
```

---

## 📚 Changelog

See [CHANGELOG.md](CHANGELOG.md) for version history and breaking changes.

**Latest Version: 4.0.0**
- PHP 8.1+ and Laravel 10+ support
- Modern type hints and strict types
- Bearer token and modern auth support
- Optional message validation
- Enhanced notification system
- Comprehensive test suite

---

## 🐛 Troubleshooting

### SMS Not Sending

1. **Check logs:** `storage/logs/laravel.log`
2. **Verify config:** Ensure gateway credentials are correct
3. **Test manually:** 
   ```bash
   php artisan tinker
   >>> smsapi()->sendMessage("YOUR_NUMBER", "Test")->response();
   ```

### Invalid Response

```php
$sms = smsapi()->sendMessage("9876543210", "Test");
dd([
    'code' => $sms->getResponseCode(),
    'response' => $sms->response(),
    'success' => $sms->isSuccessful()
]);
```

### Configuration Not Loading

```bash
php artisan config:clear
php artisan cache:clear
```

---

## 💡 Best Practices

1. **Store credentials in `.env`**, never hardcode
2. **Use queued notifications** for bulk SMS:
   ```php
   $user->notify(new WelcomeNotification());  // Use ShouldQueue trait
   ```
3. **Implement retry logic** for critical messages
4. **Monitor costs** using `estimateSegments()`
5. **Test with mock gateway** before production
6. **Log all SMS** for audit trail

---

## 🤝 Support

- **Issues:** [GitHub Issues](https://github.com/gr8shivam/laravel-sms-api/issues)
- **Documentation:** [README.md](https://github.com/gr8shivam/laravel-sms-api)

---

## 🙏 Credits

Developed by [Shivam Agarwal](https://github.com/gr8shivam)

---

## 📄 License

MIT License - see [LICENSE](LICENSE) file for details.

---

## ⭐ Show Your Support

If this package helps you, please give it a star on [GitHub](https://github.com/gr8shivam/laravel-sms-api)!

---

**Made with ❤️ for the Laravel community**