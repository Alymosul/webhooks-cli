<?php

namespace Tests\Feature;

use App\Models\Job;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class ProcessesJobsTest extends TestCase
{
    /** @test */
    function it_sends_post_request_to_registered_callbackUrl_with_given_message()
    {
        $event = $this->createFakeEvent();

        $webhook = $event->addWebhook('http://google.com');

        $webhook->schedule('Hello World!');

        Artisan::call('process');

        $jobFromDB = Job::first();

        $this->assertNotEquals('inprogress', $jobFromDB->status);
        $this->assertEquals('Hello World!', $jobFromDB->message);
    }
}
