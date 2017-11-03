<?php

namespace Gr8Shivam\SmsApi\Tests;

use Orchestra\Testbench\TestCase;
use Gr8Shivam\SmsApi\SmsApiServiceProvider;

abstract class AbstractTestCase extends TestCase
{
    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            SmsApiServiceProvider::class,
        ];
    }

    /**
     * Get package aliases.
     *
     * @param  \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageAliases($app)
    {
        return [
            'SmsApi' => \Gr8Shivam\SmsApi\SmsApiFacade::class,
        ];
    }
}
