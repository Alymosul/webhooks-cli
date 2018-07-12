<?php

namespace App\Services\Jobs\Reactors;

use App\Models\Job;

class SuccessReactor implements ReactorInterface
{
    /**
     * Handles the logic of the reactor.
     *
     * @param Job $job
     *
     * @return void
     */
    public function handle(Job $job)
    {
        $job->markAsSuccessful();
    }
}
