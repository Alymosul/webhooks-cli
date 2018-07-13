<?php

namespace App\Services\Jobs;

use App\Models\Job;
use App\Services\HttpCalls\HttpCaller;
use App\Services\Jobs\Reactors\HttpCallsReactor;

class JobsManager
{
    /**
     * Makes http calls.
     *
     * @var HttpCaller
     */
    private $httpCaller;

    /**
     * Reacts to the httpCaller response.
     *
     * @var HttpCallsReactor
     */
    private $reactor;

    /**
     * JobsManager constructor.
     *
     * @param HttpCaller $httpCaller
     * @param HttpCallsReactor $reactor
     */
    public function __construct(HttpCaller $httpCaller, HttpCallsReactor $reactor)
    {
        $this->httpCaller = $httpCaller;
        $this->reactor = $reactor;
    }

    /**
     * Executes the jobs one by one.
     *
     * @param Job[] ...$jobs
     *
     * @return array
     */
    public function execute(Job ...$jobs)
    {
        $result = [true => 0, false => 0];

        foreach ($jobs as $job) {
            $response = $this->httpCaller->hit($job->webhook->callback_url, $job->message);

            $this->reactor->getStrategy($response)->handle($job);

            $job->release();

            $result[$response]++;
        }

        return $result;
    }

    /**
     * Prevents any other process from executing the given jobs.
     *
     * @param Job[] ...$jobs
     */
    public function declareControlOver(Job ...$jobs)
    {
        foreach ($jobs as $job) {
            $job->lock();
        }
    }

    /**
     * Gets all the jobs that are ready to be executed.
     *
     * @return \ArrayAccess
     */
    public function getScheduledJobs()
    {
        return Job::getScheduledJobs();
    }

    /**
     * Detects if next cycles of processes should be stopped.
     *
     * @return bool
     */
    public function shouldStopFutureExecution()
    {
        return Job::unresolvedJobsExist();
    }

    /**
     * Forces the current process to release the given jobs.
     *
     * @param Job[] ...$jobs
     */
    public function forceRelease(Job ...$jobs)
    {
        foreach ($jobs as $job) {
            $job->release();
        }
    }
}
