<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Database\Console\Migrations\FreshCommand;
use Illuminate\Database\Console\Migrations\MigrateCommand;
use Illuminate\Support\Facades\Artisan;
use LaravelZero\Framework\Commands\Command;
use LaravelZero\Framework\Components\AbstractInstaller;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InstallWebhooksCLI extends AbstractInstaller
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'install';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Installs the application.';

    /**
     * Executes the command.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->install();
    }

    /**
     * Installs a component.
     *
     * @return void
     */
    public function install(): void
    {
        $this->composer->install();

        $this->output->newLine();

        Artisan::call('migrate:fresh', ['--seed' => true]);

        $this->output->writeln(Artisan::output());
    }
}
