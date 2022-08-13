<?php

namespace ModelFilter;

use ModelFilter\Console\MakeCommand;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

class ModelFilterServiceProvider extends LaravelServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/modelfilter.php' => config_path('modelfilter.php')
            ]);
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->commands(MakeCommand::class);
    }
}
