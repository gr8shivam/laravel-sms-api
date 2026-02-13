<?php

declare(strict_types=1);

namespace Gr8Shivam\SmsApi\Notifications;

use Gr8Shivam\SmsApi\SmsApi;
use Illuminate\Notifications\Notification;
use Psr\Log\LoggerInterface;

/**
 * Laravel Notification Channel for SMS API
 *
 * Enables Laravel's notification system to send SMS through configured gateways.
 * Automatically handles routing, message formatting, and error logging.
 */
class SmsApiChannel
{
    /**
     * Create a new SMS API channel instance
     *
     * @param SmsApi $client SMS API client instance
     * @param LoggerInterface|null $logger Optional logger for debugging
     */
    public function __construct(
        protected SmsApi $client,
        protected ?LoggerInterface $logger = null
    ) {
    }

    /**
     * Send the given notification via SMS
     *
     * @param mixed $notifiable Notifiable entity (e.g., User model)
     * @param Notification $notification Notification instance
     * @return void
     * @throws \Exception When notification doesn't implement toSmsApi() method
     */
    public function send(mixed $notifiable, Notification $notification): void
    {
        // Verify notification implements required method
        if (!method_exists($notification, 'toSmsApi')) {
            throw new \Exception(get_class($notification) . ' must implement toSmsApi() method.');
        }

        // Get recipient's mobile number
        $mobile = $notifiable->routeNotificationFor('smsapi', $notification);

        if (empty($mobile)) {
            $this->logger?->warning('No mobile number found for notification', [
                'notifiable' => get_class($notifiable),
                'notification' => get_class($notification),
            ]);
            return;
        }

        // Get message content
        $message = $notification->toSmsApi($notifiable);

        // Auto-convert string to SmsApiMessage
        if (is_string($message)) {
            $message = new SmsApiMessage($message);
        }

        // Validate message type
        if (!$message instanceof SmsApiMessage) {
            throw new \Exception('toSmsApi() must return SmsApiMessage or string');
        }

        // Use specific gateway if specified, otherwise use default
        $client = $this->client;

        if (!empty($message->gateway)) {
            $client = clone $this->client;
            $client->gateway($message->gateway);
        }

        try {
            $client->sendMessage(
                $mobile,
                $message->content,
                $message->params,
                $message->headers
            );

            $this->logger?->info('SMS notification sent', [
                'to' => $mobile,
                'notification' => get_class($notification),
            ]);
        } catch (\Exception $e) {
            $this->logger?->error('SMS notification failed', [
                'to' => $mobile,
                'notification' => get_class($notification),
                'error' => $e->getMessage(),
            ]);

            // Configurable failure handling
            $failSilently = config('sms-api.notifications.fail_silently', true);

            if (!$failSilently) {
                throw $e;
            }
        }
    }
}
