<?php

namespace App\Commands;

use App\Models\Job;
use App\Services\HttpCalls\HttpCaller;
use App\Services\Jobs\Reactors\HttpCallsReactor;
use Illuminate\Console\Scheduling\Schedule;
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
        $result = [true => 0, false => 0];

        $jobs = Job::getScheduledJobs();

        $jobs->each->lock();

        $jobs->each(function (Job $job) use (&$result) {
            $response = $this->httpCaller->hit($job->webhook->callback_url, $job->message);

            HttpCallsReactor::getStrategy($response)->handle($job);

            $job->release();

            $result[$response]++;
        });

        $this->output->writeln("Executed jobs: {$result[true]} success, {$result[false]} fail.");
    }

    /**
     * Keeps processing jobs until no scheduled jobs exist.
     *
     * @param Schedule $schedule
     */
    public function schedule(Schedule $schedule): void
    {
        if (Job::unresolvedJobsExist()) {
            $schedule->command(static::class)->everyMinute();
        }
    }
}
