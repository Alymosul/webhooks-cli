<?php

namespace App\Services\Jobs\Reactors;

use App\Models\Job;
use Illuminate\Support\Carbon;

class FailReactor implements ReactorInterface
{
    /**
     * Defines the wait time in minutes relative to the number of failures.
     */
    const WAIT_TIME = [1,5,10,30,60,120,300,600,1440];

    /**
     * Handles the logic of the reactor.
     *
     * @param Job $job
     *
     * @return void
     */
    public function handle(Job $job)
    {
        $retryAt = $this->generateNextRetryTime($job);

        $job->markAsFailed((int) $job->retries, $retryAt);
    }

    /**
     * Generates the next retry time for the given job.
     *
     * @param Job $job
     *
     * @return Carbon|null
     */
    protected function generateNextRetryTime(Job $job)
    {
        try {
            $additionalMinutes = static::WAIT_TIME[(int) $job->retries];
        } catch (\ErrorException $exception) {
            return null;
        }

        return Carbon::now()->addMinutes($additionalMinutes);
    }
}
