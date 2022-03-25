<?php

namespace App\Listeners;

use App\Events\SavingsCreationProcessed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
class SavingCreationNotification
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
     * @param  \App\Events\SavingsCreationProcessed  $event
     * @return void
     */
    public function handle(SavingsCreationProcessed $event)
    {
        $client = new \GuzzleHttp\Client();
        $response = $client->request('POST', 'https://api.sendchamp.com/api/v1/sms/send', [
            'body' => '{"to":["'.$event->phone.'"],"sender_name":"Alertapay","message":"'.$event->data.'","route":"dnd"}',
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer '.env('SENDCHAMP'),
                'Content-Type' => 'application/json',
            ],
        ]);
        $vom = $response->getBody();
        Log::alert($vom);
        Log::alert($event->data);
        Log::alert($event->phone);
    }
}
