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
        $job->markAsFailed((int) $job->retries, $this->generateNextRetryTime($job));

        // TODO: still needs retrigerring implementation..
    }

    /**
     * Generates the next retry time for the given job.
     *
     * @param Job $job
     *
     * @return Carbon
     */
    private function generateNextRetryTime(Job $job)
    {
        $additionalMinutes = static::WAIT_TIME[(int) $job->retries];

        return Carbon::now()->addMinutes($additionalMinutes);
    }
}
