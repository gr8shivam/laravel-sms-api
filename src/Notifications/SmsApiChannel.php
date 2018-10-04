<?php

namespace Gr8Shivam\SmsApi\Notifications;

use Gr8Shivam\SmsApi\Notifications\SmsApiMessage;
use Gr8Shivam\SmsApi\SmsApi;
use Illuminate\Notifications\Notification;

class SmsApiChannel
{
    /** @var Client */
    protected $client;

    /**
     * @param SmsApi $client
     */
    public function __construct(SmsApi $client) {
        $this->client = $client;
    }

    /**
     * Send the given notification.
     *
     * @param  mixed $notifiable
     * @param  \Illuminate\Notifications\Notification $notification
     * @return void
     * @throws \Gr8Shivam\SmsApi\Exception\InvalidMethodException
     */
    public function send($notifiable, Notification $notification)
    {
        if (! $mobile = $notifiable->routeNotificationFor('sms_api')) {
            return;
        }

        $message = $notification->toSmsApi($notifiable);

        if (is_string($message)) {
            $message = new SmsApiMessage($message);
        }

        $this->client->sendMessage($mobile,$message->content,$message->params,$message->headers);
    }
}