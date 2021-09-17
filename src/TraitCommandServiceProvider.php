<?php

namespace matt127127\TraitCommand;

use Illuminate\Support\ServiceProvider;
use matt127127\TraitCommand\Commands\CreateTraitCommand;

class TraitCommandServiceProvider extends ServiceProvider
{

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->runningInConsole())
        {
            $this->commands([
                CreateTraitCommand::class
            ]);
        }
    }

    public function boot()
    {

    }
}
