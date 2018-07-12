<?php

namespace Tests\Feature;

use App\Models\Job;
use App\Models\Event;
use App\Models\Webhook;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class ScheduleWebhookJobTest extends TestCase
{
    /**
     * Asserts that a job was created for the given webhook with the given message.
     *
     * @param Job $job
     * @param Webhook $webhook
     * @param string $jobMessage
     *
     * @return void
     */
    private function assertJobCreatedWithCorrectData(Job $job, Webhook $webhook, string $jobMessage)
    {
        $expectedData = [
            'webhook_id' => $webhook->id,
            'message' => $jobMessage,
            'status' => 'inprogress',
            'last_call_at' => null,
            'retries' => 0,
            'retry_at' => null
        ];

        $this->assertArraySubset($expectedData, $job->toArray());
    }

    /** @test */
    function it_creates_a_in_progress_job_for_all_the_registered_callbackUrls_for_an_existing_event()
    {
        $fakeEvent = Event::create(['name' => 'fake-event']);

        $webhook = $fakeEvent->addWebhook('http://one.com');
        $webhook2 = $fakeEvent->addWebhook('http://two.com');

        Artisan::call('dispatch', [
            'eventName' => $fakeEvent->name,
            'message' => 'Hello World!'
        ]);

        $jobs = Job::all();
        $this->assertEquals($jobs->count(), 2);

        $this->assertJobCreatedWithCorrectData($jobs->first(), $webhook, 'Hello World!');

        $this->assertJobCreatedWithCorrectData($jobs->last(), $webhook2, 'Hello World!');
    }

    /**
     * @test
     *
     * @expectedException \App\Exceptions\CouldNotFindEvent
     */
    function it_cannot_create_a_job_to_a_non_existing_event()
    {
        Artisan::call('dispatch', [
            'eventName' => 'non-existing',
            'message' => 'Hello World!'
        ]);

        $jobs = Job::all();
        $this->assertEquals($jobs->count(), 0);
    }

    /**
     * @test
     *
     * @expectedException \App\Exceptions\NoRegisteredWebHooks
     */
    function it_cannot_create_a_job_if_no_webhooks_are_registered()
    {
        $fakeEvent = Event::create(['name' => 'fake-event']);

        Artisan::call('dispatch', [
            'eventName' => $fakeEvent->name,
            'message' => 'Hello World!'
        ]);

        $jobs = Job::all();
        $this->assertEquals($jobs->count(), 0);
    }
}
