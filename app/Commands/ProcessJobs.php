<?php

namespace App\Commands;

use App\Models\Job;
use App\Services\HttpCalls\HttpCaller;
use App\Services\Jobs\Reactors\HttpCallsReactor;
use GuzzleHttp\Client;
use LaravelZero\Framework\Commands\Command;

class ProcessJobs extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'process';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Triggers callbacks.';

    /**
     * HttpCaller instance.
     *
     * @var HttpCaller
     */
    private $httpCaller;

    /**
     * ProcessJobs constructor.
     *
     * @param HttpCaller $httpCaller
     */
    public function __construct(HttpCaller $httpCaller)
    {
        $this->httpCaller = $httpCaller;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        //Get all scheduled jobs that are not successful..
        $jobs = Job::getScheduledJobs();

        $jobs->each(function (Job $job) {
            // Process the new jobs and the jobs that the retry_at matches the current time.
            $response = $this->httpCaller->hit($job->webhook->callback_url, $job->message);

            HttpCallsReactor::getStrategy($response)->handle($job);
        });

        // each job either successful or failed...if failed it should be marked to retry at certain time..

    }
}
