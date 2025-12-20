<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;


class SendFcmNotificationJob implements ShouldQueue
{
    
     use Dispatchable, Queueable;

    public $token;
    public $title;
    public $body;

    /**
     * Create a new job instance.
     */
    public function __construct($token, $title, $body)
    {
        $this->token = $token;
        $this->title = $title;
        $this->body = $body;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
         if (!$this->token) {
            return;
        }

        $data = [
            "to" => $this->token,
            "notification" => [
                "title" => $this->title,
                "body" => $this->body,
                "sound" => "default"
            ]
        ];

        $headers = [
            "Authorization: key=" . env('FCM_SERVER_KEY'),
            "Content-Type: application/json"
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://fcm.googleapis.com/fcm/send");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_exec($ch);
        curl_close($ch);
    }
}
