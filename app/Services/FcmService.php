<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Illuminate\Support\Facades\Log;


class FcmService
{
    protected $messaging;

    public function __construct()
    {
        $factory = (new Factory)
            ->withServiceAccount(
                storage_path('app/food-6d38b-5ecd62ee1630.json')
            );

        $this->messaging = $factory->createMessaging();
    }

    /**
     * إرسال إشعار FCM
     */
   public function send(
    ?string $token,
    string $title,
    string $body,
    array $data = []
): void {
    if (! $token) {
        return;
    }

    try {
        $message = CloudMessage::fromArray([
            'token' => $token,
            'notification' => [
                'title' => $title,
                'body'  => $body,
            ],
            'data' => $data,
        ]);

        $this->messaging->send($message);

    } catch (\Throwable $e) {
        Log::error('FCM send failed', [
            'error' => $e->getMessage(),
            'token' => substr($token, 0, 10) . '...',
        ]);
    }
}
}