<?php

namespace Tests\Feature;

use App\Models\Job;
use App\Models\Webhook;
use App\Services\HttpCalls\HttpCaller;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class ProcessesJobsTest extends TestCase
{
    /**
     * Data provider for the wait time for failed jobs.
     *
     * @return array
     */
    public function waitTime()
    {
        return [
            [1, 1],
            [2, 5],
            [3, 10],
            [4, 30],
            [5, 60],
            [6, 120],
            [7, 300],
            [8, 600],
            [9, 1440]
        ];
    }

    /**
     * Schedules a fake job for a given webhook with the given attributes.
     *
     * @param Webhook $webhook
     * @param array $attributes
     *
     * @return Job|Model
     */
    private function createFakeJob(Webhook $webhook, $attributes = ['message' => 'Hello World!'])
    {
        return $webhook->job()->create($attributes);
    }

    /**
     * Asserts that a job was successful.
     *
     * @param Job $job
     *
     * @return void
     */
    private function assertJobWasSuccessful(Job $job)
    {
        $this->assertEquals('success', $job->status);
        $this->assertEquals(0, $job->retries);
        $this->assertEquals(null, $job->retry_at);
    }

    /** @test */
    function it_changes_job_status_after_sending_post_request_to_registered_callbackUrl_with_given_message()
    {
        $event = $this->createFakeEvent();

        $webhook = $event->addWebhook('http://google.com');

        $this->createFakeJob($webhook);

        Artisan::call('process');

        $jobFromDB = Job::first();

        $this->assertNotEquals('inprogress', $jobFromDB->status);
        $this->assertEquals('Hello World!', $jobFromDB->message);
    }

    /** @test */
    function it_marks_successful_jobs_and_nullifies_other_properties_of_the_job()
    {
        $mockHttpCaller = $this->createMock(HttpCaller::class);
        $this->app->instance(HttpCaller::class, $mockHttpCaller);
        $mockHttpCaller->method('hit')->willReturn(true);

        $event = $this->createFakeEvent();

        $webhook = $event->addWebhook('http://google.com');

        $this->createFakeJob($webhook, [
            'message' => 'Hello, World!',
            'last_call_at' => Carbon::now(),
            'retries' => 2,
            'retry_at' => Carbon::now()->subMinutes(2),
        ]);

        Artisan::call('process');

        $this->assertJobWasSuccessful(Job::first());
    }

    /**
     * @test
     *
     * @dataProvider waitTime
     */
    function it_marks_failed_jobs_and_sets_the_number_of_retries_as_well_as_the_next_retry_time($numberOfretries, $additionalMinutes)
    {
        $event = $this->createFakeEvent();

        $webhook = $event->addWebhook('http://google.com');

        $this->createFakeJob($webhook, [
            'webhook_id' => $webhook->id,
            'message'    => 'Hello World!',
            'retries'    => $numberOfretries - 1,
        ]);

        Artisan::call('process');

        $jobFromDB = Job::first();

        $this->assertEquals('fail', $jobFromDB->status);
        $this->assertEquals($numberOfretries, $jobFromDB->retries);
        $this->assertEquals(Carbon::now()->addMinutes($additionalMinutes)->format('Y-m-d H:i'), Carbon::parse($jobFromDB->retry_at)->format('Y-m-d H:i'));
    }

    /** @test */
    function it_ignores_jobs_that_has_retry_at_in_the_future()
    {
        $event = $this->createFakeEvent();

        $webhook = $event->addWebhook('http://google.com');

        $job = $this->createFakeJob($webhook, [
            'message' => 'Hello, World!',
            'status' => 'fail',
            'last_call_at' => Carbon::now()->toDateTimeString(),
            'retries' => 5,
            'retry_at' => Carbon::now()->addHour()->toDateTimeString(),
            'locked' => false
        ]);

        Artisan::call('process');

        $this->assertEquals($job->toArray(), Job::first()->toArray());
    }

    /** @test */
    function it_ignores_jobs_that_are_locked_by_other_processJobs_command()
    {
        $event = $this->createFakeEvent();

        $webhook = $event->addWebhook('http://google.com');

        $job = $this->createFakeJob($webhook, [
            'message' => 'Hello, World!',
            'status' => 'inprogress',
            'last_call_at' => null,
            'retries' => 0,
            'retry_at' => null,
            'locked' => true
        ]);

        Artisan::call('process');

        $this->assertEquals($job->toArray(), Job::first()->toArray());
    }

    /** @test */
    function it_releases_jobs_at_the_end_of_execution()
    {
        $event = $this->createFakeEvent();

        $webhook = $event->addWebhook('http://google.com');

        $this->createFakeJob($webhook);

        Artisan::call('process');

        $this->assertEquals(false, Job::first()->locked);
    }
}
