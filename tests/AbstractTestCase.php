<?php

namespace Gr8Shivam\SmsApi\Tests;

use Orchestra\Testbench\TestCase;
use Gr8Shivam\SmsApi\SmsApiServiceProvider;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

abstract class AbstractTestCase extends TestCase
{
    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('sms-api.default', 'test_gateway');
        $app['config']->set('sms-api.country_code', '91');
        $app['config']->set('sms-api.test_gateway', [
            'method' => 'POST',
            'url' => 'http://example.com/sms',
            'params' => [
                'send_to_param_name' => 'mobile',
                'msg_param_name' => 'message',
                'others' => ['api_key' => 'test_key'],
            ],
            'headers' => [],
            'add_code' => true,
        ]);
    }

    protected function getPackageProviders($app): array
    {
        return [SmsApiServiceProvider::class];
    }

    protected function getPackageAliases($app): array
    {
        return [
            'SmsApi' => \Gr8Shivam\SmsApi\SmsApiFacade::class,
        ];
    }
    
    // ✅ Add mock helper
    protected function mockSmsGateway(int $statusCode = 200, string $body = 'OK'): void
    {
        $mock = new MockHandler([
            new Response($statusCode, [], $body),
        ]);
        
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);
        
        $this->app->instance('smsapi', new \Gr8Shivam\SmsApi\SmsApi($client));
    }
}
