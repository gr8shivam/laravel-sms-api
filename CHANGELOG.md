# Changelog

All notable changes to `laravel-sms-api` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [4.0.0] - 2026-02-13

### Added
- PHP 8.1+ support with strict types
- Laravel 10.x, 11.x, and 12.x compatibility
- Modern type hints throughout the codebase
- Bearer token authentication support
- Custom header authentication support
- Input validation for phone numbers and messages
- URL validation for gateway endpoints
- Configuration validation on load
- `isSuccessful()` method to check 2xx status codes
- `send_sms()` helper function with exception handling
- Optional message validation with configurable max length
- Configurable `fail_silently` for notification channel
- Laravel Octane compatibility with proper state management
- Comprehensive PHPDoc blocks for all public methods
- `estimateSegments()` method for SMS cost estimation
- `preview()` method for message truncation
- Static `create()` constructor for fluent message building
- Support for `ext-json` and `ext-mbstring` in composer requirements
- Proper exception handling for connection timeouts
- Logger integration in notification channel

### Changed
- Upgraded minimum PHP version to 8.1
- Upgraded minimum Laravel version to 10.0
- Replaced mixed types with proper union types (string|array)
- Changed `SmsApi` service binding from singleton to bind (for state safety)
- Improved error messages with actionable information
- Enhanced configuration structure with validation settings
- Better bulk SMS handling with array support
- Improved notification channel with cloned client for gateway switching
- POST requests now use `form_params` instead of `query` for non-JSON payloads

### Fixed
- POST form-data bug where parameters were sent in URL instead of body
- State pollution issues in Laravel Octane
- Empty response code handling in `isSuccessful()`
- Missing connection error handling for network timeouts
- Wrapper parameters not resetting between requests
- Double asterisk PHPDoc syntax error in Facade

### Removed
- PHP 7.x support
- Laravel 9.x and older support
- Deprecated methods and configurations

### Security
- Added comprehensive input validation
- URL validation with `filter_var()`
- Phone number validation to prevent empty/invalid entries
- Message content validation to prevent empty messages
- Configuration parameter validation

---

## Upgrade Guide

### Upgrading to 4.0 from 3.x

#### PHP & Laravel Requirements
- Upgrade PHP to 8.1 or higher
- Upgrade Laravel to 10.0 or higher

#### Type Hints
All methods now use strict types. Update your code if you were passing non-string/non-array values:

```php
// Before (may have worked with loose types)
smsapi(123456, $message);

// After (strict types required)
smsapi('123456', $message);
```

#### Configuration Changes
Add new validation settings to your `config/sms-api.php`:

```php
'validation' => [
    'enabled' => env('SMS_API_VALIDATION_ENABLED', false),
    'max_length' => env('SMS_API_MAX_LENGTH', 1000),
],

'notifications' => [
    'fail_silently' => env('SMS_API_NOTIFICATIONS_FAIL_SILENTLY', true),
],
```

#### POST Requests
If you have custom gateway configurations using POST with non-JSON format, they will now work correctly (previously params were incorrectly sent in URL).

#### Success Checking
Update response checking:

```php
// Before
if ($response->getResponseCode() >= 200 && $response->getResponseCode() < 300) {
    // Success
}

// After (preferred)
if ($response->isSuccessful()) {
    // Success
}
```

#### Helper Function
Update `send_sms()` usage if you relied on exceptions:

```php
// Before (exceptions were thrown)
try {
    send_sms($phone, $message);
} catch (Exception $e) {
    // Handle error
}

// After (returns boolean, logs automatically)
if (send_sms($phone, $message)) {
    // Success
} else {
    // Failed (already logged)
}
```

---

## Support

For questions, issues, or feature requests:
- [Open an Issue](https://github.com/gr8shivam/laravel-sms-api/issues)
- [View Documentation](https://github.com/gr8shivam/laravel-sms-api#readme)
