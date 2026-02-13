<?php

declare(strict_types=1);

namespace Gr8Shivam\SmsApi;

use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Support\DeferrableProvider;
use Gr8Shivam\SmsApi\Notifications\SmsApiChannel;

class SmsApiServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Bootstrap any package services
     */
    public function boot(): void
    {
        $configPath = __DIR__ . '/config/sms-api.php';
        $examplesPath = __DIR__ . '/config/sms-api-examples.php';
        
        // Publish main configuration
        $this->publishes([
            $configPath => config_path('sms-api.php')
        ], 'sms-api-config');

        // Publish examples configuration
        $this->publishes([
            $examplesPath => config_path('sms-api-examples.php')
        ], 'sms-api-examples');
    }

    /**
     * Register any package services
     */
    public function register(): void
    {
        $configPath = __DIR__ . '/config/sms-api.php';
        $this->mergeConfigFrom($configPath, 'sms-api');
        
        // Register Guzzle Client as singleton (stateless, safe to reuse)
        $this->app->singleton('sms.http.client', function ($app) {
            return new Client([
                'timeout' => config('sms-api.timeout', 30),
                'connect_timeout' => config('sms-api.connect_timeout', 10),
            ]);
        });
        
        // Register SmsApi as bind (stateful, fresh instance per request)
        $this->app->bind('smsapi', function ($app) {
            return new SmsApi($app->make('sms.http.client'));
        });
        
        // Register Notification Channel with logger
        $this->app->bind(SmsApiChannel::class, function ($app) {
            return new SmsApiChannel(
                $app->make('smsapi'),
                $app->make('log')
            );
        });
        
        $this->app->alias('smsapi', SmsApi::class);
    }

    /**
     * Get the services provided by the provider
     *
     * @return array
     */
    public function provides(): array
    {
        return ['smsapi', SmsApi::class, 'sms.http.client', SmsApiChannel::class];
    }
}
