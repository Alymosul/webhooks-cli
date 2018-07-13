<?php

namespace App\Commands;

use App\Services\Jobs\JobsManager;
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
     * Manager instance.
     *
     * @var JobsManager
     */
    private $jobsManager;

    /**
     * ProcessJobs constructor.
     *
     * @param JobsManager $jobsManager
     */
    public function __construct(JobsManager $jobsManager)
    {
        $this->jobsManager = $jobsManager;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $jobs = $this->jobsManager->getScheduledJobs();

        try {
            $this->jobsManager->declareControlOver(...$jobs);

            list($numberOfSuccess, $numberOfFail) = $this->jobsManager->execute(...$jobs);
        } catch (\Exception $exception) {
            $this->output->writeln('Internal error occurred while executing jobs.');

            $this->jobsManager->forceRelease(...$jobs);
            return;
        }

        $this->output->writeln("Executed jobs: {$numberOfSuccess} success, {$numberOfFail} fail.");
    }

    /**
     * Keeps processing jobs until no scheduled jobs exist.
     *
     * @param Schedule $schedule
     */
    public function schedule(Schedule $schedule): void
    {
        if ($this->jobsManager->shouldStopFutureExecution()) {
            $schedule->command(static::class)->everyMinute();
        }
    }
}
