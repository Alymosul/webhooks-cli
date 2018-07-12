<?php

namespace App\Commands;

use App\Exceptions\CouldNotCreateWebhook;
use App\Exceptions\CouldNotFindEvent;
use App\Models\Event;
use App\Validators\CreateWebhookValidator;
use LaravelZero\Framework\Commands\Command;

class CreateWebhook extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'create {eventName} {callbackUrl}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Subscribes for the given event name.';

    /**
     * @var CreateWebhookValidator
     */
    private $createWebhookValidator;

    /**
     * CreateWebhook constructor.
     *
     * @param CreateWebhookValidator $createWebhookValidator
     */
    public function __construct(CreateWebhookValidator $createWebhookValidator)
    {
        $this->createWebhookValidator = $createWebhookValidator;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     *
     * @throws CouldNotCreateWebhook
     */
    public function handle(): void
    {
        $eventName = $this->input->getArgument('eventName');
        $callbackUrl = $this->input->getArgument('callbackUrl');

        $this->createWebhookValidator->validateCallbackUrl($callbackUrl);

        $event = Event::getByName($eventName);

        $event->addWebhook($callbackUrl);

        $this->output->writeln("New webhook was subscribed to {$eventName}.");
    }
}
