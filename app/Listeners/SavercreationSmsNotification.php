<?php

namespace App\Listeners;

use App\Events\SavercreationSms;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SavercreationSmsNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\SavercreationSms  $event
     * @return void
     */
    public function handle(SavercreationSms $event)
    {

        $client = new \GuzzleHttp\Client();

        $response = $client->request('POST', 'https://api.sendchamp.com/api/v1/sms/send', [
            'body' => '{"to":["2349034426192"],"sender_name":"Alertapay","message":"Ajosuite  O . T . P setup successfully '.$event->otp.'","route":"dnd"}',
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer sendchamp_live_$2y$10$ZLo8Abxt7D4o3Y1qk2ugP.bFEJlFupR5ijW360aNXJjRDs10zrPam',
                'Content-Type' => 'application/json',
            ],
        ]);
        $vom = $response->getBody();
        //event->otp
        Log::alert($vom);
    }
}
