<?php

namespace App\Commands;

use App\Exceptions\NoRegisteredWebHooks;
use App\Models\Event;
use App\Models\Webhook;
use LaravelZero\Framework\Commands\Command;

class ScheduleWebhookJob extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'dispatch {eventName} {message}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Dispatches event name with given message.';

    /**
     * Execute the console command.
     *
     * @return void
     *
     * @throws NoRegisteredWebHooks
     */
    public function handle(): void
    {
        $message = $this->input->getArgument('message');

        $event = Event::getByName($this->input->getArgument('eventName'));

        $webhooks = $event->webhooks;

        if ($webhooks->count() == 0) {
            throw new NoRegisteredWebhooks($event->name);
        }

        $webhooks->each->schedule($message);

        $this->output->writeln('Scheduling done.');
    }
}
