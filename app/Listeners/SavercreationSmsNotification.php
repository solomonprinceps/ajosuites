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
            'body' => '{"to":["'.$event->phone.'"],"sender_name":"Alertapay","message":"Ajosuite  O . T . P setup successfully '.$event->otp.'","route":"dnd"}',
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer '.env('SENDCHAMP'),
                'Content-Type' => 'application/json',
            ],
        ]);
        $vom = $response->getBody();
        // event->otp
        Log::alert($vom);
        // Log::alert($event->phone. "phone");
    }
}
