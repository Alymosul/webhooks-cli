<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\RefreshDatabaseState;
use Illuminate\Support\Facades\Artisan;

abstract class TestCase extends LaravelTestCase
{
    use CreatesApplication, RefreshDatabase;
    
    /**
     * Tweaks the refreshDatabase.
     *
     * @return void
     */
    public function refreshDatabase()
    {
        if (! RefreshDatabaseState::$migrated) {
            Artisan::call('migrate:fresh', $this->shouldDropViews() ? [
                '--drop-views' => true,
            ] : []);

            $this->app[Kernel::class]->setArtisan(null);

            RefreshDatabaseState::$migrated = true;
        }

        $this->beginDatabaseTransaction();
    }
}
