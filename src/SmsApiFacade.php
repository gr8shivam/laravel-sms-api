<?php
namespace Gr8Shivam\SmsApi;

class SmsApiFacade extends \Illuminate\Support\Facades\Facade
{
    protected static function getFacadeAccessor()
    {
        return 'smsapi';
    }
}