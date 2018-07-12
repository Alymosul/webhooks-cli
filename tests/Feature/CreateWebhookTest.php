<?php

namespace Tests\Feature;

use App\Models\Webhook;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class CreateWebhookTest extends TestCase
{
    /** @test */
    function it_creates_a_webhook_to_an_existing_event_in_the_database()
    {
        $event = $this->createFakeEvent();

        $webhookDetails = [
            'eventName' => $event->name,
            'callbackUrl' => 'http://google.com'
        ];

        Artisan::call('create', $webhookDetails);

        $webhookFromDB = Webhook::first();

        $this->assertEquals($webhookDetails['callbackUrl'], $webhookFromDB->callback_url);
    }

    /**
     * @test
     *
     * @expectedException \App\Exceptions\CouldNotCreateWebhook
     */
    function it_cannot_create_a_webhook_with_an_invalid_callbackUrl()
    {
        Artisan::call('create', [
            'eventName' => 'Event1',
            'callbackUrl' => 'something'
        ]);

        $this->assertEquals(Webhook::count(), 0);
    }

    /**
     * @test
     *
     * @expectedException \App\Exceptions\CouldNotFindEvent
     */
    function it_cannot_create_a_webhook_to_a_non_existing_event()
    {
        Artisan::call('create', [
            'eventName' => 'Event1',
            'callbackUrl' => 'http://google.com'
        ]);

        $this->assertEquals(Webhook::count(), 0);
    }
}
