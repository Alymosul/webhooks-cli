<?php

namespace App\Services\Jobs\Reactors;

use App\Models\Job;

interface ReactorInterface
{
    /**
     * Handles the logic of the reactor.
     *
     * @param Job $job
     *
     * @return void
     */
    public function handle(Job $job);
}
