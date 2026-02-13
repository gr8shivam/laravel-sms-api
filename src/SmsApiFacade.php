<?php

declare(strict_types=1);

namespace Gr8Shivam\SmsApi;

use Illuminate\Support\Facades\Facade;

/**
 * SMS API Facade
 *
 * @method static \Gr8Shivam\SmsApi\SmsApi gateway(string $gateway)
 * @method static \Gr8Shivam\SmsApi\SmsApi countryCode(string $code)
 * @method static \Gr8Shivam\SmsApi\SmsApi addWrapperParams(array $params)
 * @method static \Gr8Shivam\SmsApi\SmsApi sendMessage(string|array $to, string $message, ?array $extra_params = null, array $extra_headers = [])
 * @method static string response()
 * @method static int|string getResponseCode()
 * @method static bool isSuccessful()
 *
 * @see \Gr8Shivam\SmsApi\SmsApi
 */
class SmsApiFacade extends Facade
{
    /**
     * Get the registered name of the component
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'smsapi';
    }
}
